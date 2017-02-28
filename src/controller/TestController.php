<?php

namespace repotracker;

use \Exception;

/**
 * Handle the management of issue filters.
 */
class TestController extends Singleton {

	/**
	 * Construct.
	 */
	public function __construct() {
		add_shortcode("repo-tracker-test",array($this,"handleTestShortcode"));
	}

	/**
	 * Handle [repo-tracker-test].
	 */
	public function handleTestShortcode() {
		wp_enqueue_style(
			"wp-repo-tracker",
			REPOTRACKER_URL."/wp-repo-tracker.css"
		);

//		$template=new Template(__DIR__."/../view/issuelist.php");
		$template=new Template(__DIR__."/../view/issuelist-postit.php");

		$issueDatas=array();
		for ($i=0; $i<10; $i++)
			$issueDatas[]=array(
				"title"=>"Test Issue with a very very long title and so on and even longer and longer and longer",
				"description"=>"Description",
				"url"=>"http://www.github.com/",
				"labels"=>array("label"),
				"numComments"=>1,
				"numAssigned"=>2,
				"state"=>"open",
				"opened"=>NULL,
				"closed"=>NULL
			);

		return $template->render(array(
			"lastError"=>NULL,
			"numIssues"=>5,
			"issues"=>$issueDatas
		));
	}
}