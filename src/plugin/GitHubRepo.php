<?php

namespace repotracker;

use \Exception;

/**
 * Fetch issues from the GitHub API.
 */
class GitHubRepo {

	private $repoOrg;
	private $repoProject;

	/**
	 * Construct.
	 * The repo id should be org/repo or full url.
	 */
	public function __construct($repoId) {
		$urlExp='/^https\:\/\/github.com\/([^\/\.]*)\/([^\/\.]*)(\.git)?$/';
		if (preg_match($urlExp,$repoId,$matches)) {
			$this->repoOrg=$matches[1];
			$this->repoProject=$matches[2];
		}

		$shortExp='/^([^\/\.]*)\/([^\/\.]*)$/';
		if (preg_match($shortExp,$repoId,$matches)) {
			$this->repoOrg=$matches[1];
			$this->repoProject=$matches[2];
		}

		if (!$this->repoOrg || !$this->repoProject)
			throw new Exception("Unknown repository: ".$repoId);
	}

	/**
	 * Get issues.
	 */
	public function getIssues() {
		$url="https://api.github.com/repos/".
			$this->repoOrg."/".$this->repoProject.
			"/issues";

		$curl=new CurlRequest($url);
		$curl->setParam("state","all");
		$curl->setResultProcessing("json");
		$issueDatas=$curl->exec();

		$issues=array();
		foreach ($issueDatas as $issueData)
			$issues[]=new RepoIssue($issueData);

		return $issues;
	}
}