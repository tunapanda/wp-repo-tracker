<?php

namespace repotracker;

/**
 * Wraps the issuefilter post type.
 */
class IssueFilter {

	private $post;

	/**
	 * Constructor.
	 * This is private, to fetch an IssueFilter, use
	 * IssueFilter::getById.
	 */
	private function __construct($post) {
		$this->post=$post;
	}

	/**
	 * Get the list of issues for this filter.
	 */
	public function getIssues() {
		return array(
			array(
				"title"=>"hello"
			),
			array(
				"title"=>"world"
			)
		);
	}

	/**
	 * Get an issue filter by id.
	 */
	public static function getById($postId) {
		if (!$postId)
			return NULL;

		$post=get_post($postId);

		if (!$post)
			return NULL;

		if ($post->post_type!="issuefilter")
			throw new Exception("This is not an issuefilter post.");

		return new IssueFilter($post);
	}

}