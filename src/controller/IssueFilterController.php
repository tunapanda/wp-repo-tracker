<?php

namespace repotracker;

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
	}

	/**
	 * Handle the issuelist shortcode.
	 */
	public function issuelist($params) {
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
	                	"open"=>"Consider only assigned issues",
	                	"closed"=>"Consider only unassigned issues"
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

		return $metaBoxes;
	}
}