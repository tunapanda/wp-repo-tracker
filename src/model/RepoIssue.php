<?php

namespace repotracker;

use \WpRecord;
use \Exception;

/**
 * Represents one issue from a repository.
 */
class RepoIssue extends \WpRecord {

	/**
	 * Constructor.
	 */
	public function __construct($issueData=NULL) {
		if ($issueData) {
			$this->issueDataJson=json_encode($issueData);
			$this->issueData=$issueData;
		}
	}

	/**
	 * Set up fields.
	 */
	public static function initialize() {
		self::field("id","integer not null auto_increment");
		self::field("issueDataJson","text");
		self::field("issueFilterId","integer not null");
	}

	/**
	 * Get issue data for this issue.
	 */
	public function getIssueData() {
		if (!$this->issueData)
			$this->issueData=json_decode($this->issueDataJson,TRUE);

		if (!$this->issueData)
			throw new Exception("Bad issue data");

		return $this->issueData;
	}

	/**
	 * Get title
	 */
	public function getTitle() {
		$issueData=$this->getIssueData();
		return $issueData["title"];
	}

	/**
	 * Get labels as an array of strings.
	 */
	public function getLabels() {
		$issueData=$this->getIssueData();
		$labels=array();

		foreach ($issueData["labels"] as $labelData)
			$labels[]=$labelData["name"];

		return $labels;
	}

	/**
	 * Get number of comments
	 */
	public function getNumComments() {
		$issueData=$this->getIssueData();
		return $issueData["comments"];
	}

	/**
	 * Get issue state (open/closed).
	 */
	public function getState() {
		$issueData=$this->getIssueData();
		return $issueData["state"];
	}

	/**
	 * Get opened timstamp.
	 */
	public function getOpenedTimestamp() {
		$issueData=$this->getIssueData();
		return strtotime($issueData["created_at"]);
	}

	/**
	 * Get closed timstamp.
	 */
	public function getClosedTimestamp() {
		$issueData=$this->getIssueData();
		return strtotime($issueData["closed_at"]);
	}

	/**
	 * Get description.
	 */
	public function getDescription() {
		$issueData=$this->getIssueData();
		return $issueData["body"];
	}

	/**
	 * Get url.
	 */
	public function getUrl() {
		$issueData=$this->getIssueData();
		return $issueData["html_url"];
	}

	/**
	 * Get login names of issue assignees.
	 */
	public function getAssigneeLogins() {
		$issueData=$this->getIssueData();
		$logins=array();

		foreach ($issueData["assignees"] as $assigneeData)
			$logins[]=$assigneeData["login"];

		return $logins;
	}

	/**
	 * Get number of assignees.
	 */
	public function getNumAssigned() {
		return sizeof($this->getAssigneeLogins());
	}
}