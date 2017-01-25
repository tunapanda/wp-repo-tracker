<?php

namespace repotracker;

use \Exception;

/**
 * Handle the management of issue filters.
 */
class IssueFilterController extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action("init",array($this,"init"));

		if (is_admin())
			add_filter("rwmb_meta_boxes",array($this,'rwmbMetaBoxes'));

		add_shortcode("issuelist",array($this,"issuelist"));
		add_shortcode("issuecount",array($this,"issuecount"));

		add_filter("the_content",array($this,"theContent"));

		$dasherooAction="repo_issue_stats_dasheroo";
		add_filter("wp_ajax_$dasherooAction",array($this,"ajaxStats"));
		add_filter("wp_ajax_nopriv_$dasherooAction",array($this,"ajaxStats"));
	}

	/**
	 * Publish information for dasheroo.
	 */
	public function ajaxStats() {
		header('Content-Type: application/json');

		try {
			if (!isset($_REQUEST["id"]) || !$_REQUEST["id"])
				throw new Exception("Issue filter id not specified.");

			$issueFilter=IssueFilter::getById($_REQUEST["id"]);
			if (!$issueFilter)
				throw new Exception("No issue filter found with this id.");

			$data=array(
				"issues"=>array(
					"type"=>"integer",
					"value"=>$issueFilter->getNumIssues(),
					"label"=>$issueFilter->getTitle(),
					"strategy"=>"continuous"
				)
			);

			echo json_encode($data,JSON_PRETTY_PRINT);
			exit;
		}

		catch (Exception $e) {
			http_response_code(500);

			$data=array(
				"error"=>true,
				"message"=>$e->getMessage()
			);

			echo json_encode($data,JSON_PRETTY_PRINT);
			exit;
		}
	}

	/**
	 * The content.
	 */
	public function theContent($content) {
		global $post;

		if ($post->post_type!="issuefilter")
			return $content;

		if (!is_single())
			return $content;

		return
			"Number of issues: [issuecount id='".$post->ID."']".
			"[issuelist id='".$post->ID."']";
	}

	/**
	 * Enqueue style.
	 */
	private function enqueueStyle() {
		wp_enqueue_style(
			"wp-repo-tracker",
			REPOTRACKER_URL."/wp-repo-tracker.css"
		);
	}

	/**
	 * Handle the issuelist shortcode.
	 */
	public function issuelist($params) {
		$this->enqueueStyle();

		$issueFilter=IssueFilter::getById($params["id"]);

		if (!$issueFilter)
			return "Issue filter not found.";

		$issueViews=array();
		$issues=$issueFilter->getIssues();
		foreach ($issues as $issue) {
			$issueView=array(
				"title"=>$issue->getTitle()
			);

			$issueViews[]=$issueView;
		}

		$params=array(
			"issues"=>$issueViews,
			"numIssues"=>$issueFilter->getNumIssues()
		);

		$template=new Template(__DIR__."/../view/issuelist.php");

		return $template->render($params);
	}

	/**
	 * Handle the issuecount shortcode.
	 */
	public function issuecount($params) {
		$this->enqueueStyle();

		$issueFilter=IssueFilter::getById($params["id"]);

		if (!$issueFilter)
			return "Issue filter not found.";

		return 
			"<span class='repo-tracker-issue-count'>".
			$issueFilter->getNumIssues().
			"</span>";
	}

	/**
	 * The WordPress init action.
	 */
	public function init() {
		register_post_type("issuefilter",array(
			"labels"=>array(
				"name"=>"Issue Filters",
				"singular_name"=>"Issue Filter",
				"not_found"=>"No Issue Filters found.",
				"add_new_item"=>"Add new Issue Filter",
				"edit_item"=>"Edit Issue Filter",
			),
			"public"=>true,
			"supports"=>array("title"),
			"show_in_nav_menus"=>false,
		));
	}

	/**
	 * Add meta boxes.
	 */
	public function rwmbMetaBoxes($metaBoxes) {
		global $wpdb;

		$metaBoxes[]=array(
	        'title'      => 'Repositories and Labels',
	        'post_types' => 'issuefilter',
	        'fields'     => array(
	            array(
	                'type' => 'text',
	                'id'   => 'repositories',
	                'name' => "Repositories",
	                'clone'=>true,
	                'sort_clone'=>true,
	                'desc'=>"Enter one or more repository URLs where to fetch issues from.<br>".
	                	"You can use the full repository URL from GitHub, e.g.<br>".
	                	"https://github.com/tunapanda/wp-swag",
	            ),
	            array(
	                'type' => 'text',
	                'id'   => 'labels',
	                'name' => "Labels",
	                'clone'=>true,
	                'sort_clone'=>true,
	                'desc'=>"Consider only issues with these labels.<br>".
	                	"E.g. current-sprint or resolved.",
	            ),
	        ),
		);

		$metaBoxes[]=array(
	        'title'      => 'Filters',
	        'post_types' => 'issuefilter',
	        'fields'     => array(
	            array(
	                'type' => 'select',
	                'id'   => 'state',
	                'name' => "State",
	                'desc'=>"Which state should be considered?",
	                "options"=>array(
	                	"all"=>"Consider both open and closed issues",
	                	"open"=>"Consider only open issues",
	                	"closed"=>"Consider only closed issues"
	                )
	            ),
	            array(
	                'type' => 'select',
	                'id'   => 'assigned',
	                'name' => "Assigned",
	                'desc'=>"Consider only assigned or unassigned issues? Or all issues?",
	                "options"=>array(
	                	"all"=>"Consider both assigned and unassigned issues",
	                	"yes"=>"Consider only assigned issues",
	                	"no"=>"Consider only unassigned issues"
	                )
	            ),
	            array(
	                'type' => 'text',
	                'id'   => 'createdLastDays',
	                'name' => "New Issues",
	                'desc'=>"Consider only issues which were created within this many days.<br>".
						"Should be a number, e.g. 7.",
	            ),
	            array(
	                'type' => 'text',
	                'id'   => 'updatedLastDays',
	                'name' => "Recently updated issues",
	                'desc'=>"Consider only issues which were updated within this many days.<br>".
						"Should be a number, e.g. 7.",
	            ),
	            array(
	                'type' => 'text',
	                'id'   => 'closedLastDays',
	                'name' => "Recently closed issues",
	                'desc'=>"Consider only issues which were closed within this many days.<br>".
						"Should be a number, e.g. 7.",
	            ),
	        ),
		);

		$metaBoxes[]=array(
	        'title'=>'Views',
	        'post_types'=>'issuefilter',
			"priority"=>"low",
			'context'=>"side",
	        'fields'=>array(
	            array(
	                'type' => 'custom_html',
	                'id'=>'issueList',
	                'name' => "Issue List",
	                'callback'=>array($this,"issuelistShortCodeInfo"),
	                'desc'=>"Use this shortcode to include the issue list in a post or page."
	            ),

	            array(
	                'type' => 'custom_html',
	                'id'=>'issueList',
	                'name' => "Issue Count",
	                'callback'=>array($this,"issuecountShortCodeInfo"),
	                'desc'=>"Use this shortcode to include the issue count in a post or page."
	            ),

	            array(
	                'type' => 'custom_html',
	                'id'=>'dasherooLink',
	                'name' => "Dasheroo",
	                'callback'=>array($this,"dasherooLinkInfo"),
	                'desc'=>"Use this link for as a data url to create ".
	                "<a href='https://www.dasheroo.com/pages/custom-insights' target='_blank'>".
	                "Dasheroo Custom Insights".
	                "</a>. Right click on the link above and copy it, then paste it into the ".
	                "Dasheroo settings form."
	            ),
	        ),
		);

		return $metaBoxes;
	}

	/**
	 * Dasheroo link info.
	 */
	public function dasherooLinkInfo() {
		global $post;

		$postId=$post->ID;
		$url=admin_url(
			'admin-ajax.php?'.
			'action=repo_issue_stats_dasheroo&'.
			'id='.$postId
		);

		return "<a href='$url'>Data URL</a>";
	}

	/**
	 * Info about the issuelist shortcode.
	 */
	public function issuelistShortCodeInfo() {
		global $post;

		$postId=$post->ID;

		return "[issuelist id='".$post->ID."']";
	}

	/**
	 * Info about the issuecount shortcode.
	 */
	public function issuecountShortCodeInfo() {
		global $post;

		$postId=$post->ID;

		return "[issuecount id='".$post->ID."']";
	}
}