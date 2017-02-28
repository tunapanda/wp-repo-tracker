<?php

namespace repotracker;

use \Exception;

/**
 * Wraps the issuefilter post type.
 */
class IssueFilter extends PostTypeModel {

	public static $posttype="issuefilter";
	public $issues=NULL;

	/**
	 * Clear issue cache.
	 */
	public function clearIssueCache() {
		$issues=RepoIssue::findAllBy("issueFilterId",$this->getId());
		foreach ($issues as $issue)
			$issue->delete();

		$this->issues=NULL;
		$this->setMeta("lastFetch",0);
		$this->setMeta("lastFetchError",NULL);
	}

	/**
	 * Populate issue cache.
	 */
	public function populateIssueCache() {
		$this->clearIssueCache();

		$this->issues=array();
		foreach ($this->getMeta("repositories") as $repoUrl) {
			$gitHubRepo=new GitHubRepo($repoUrl);

			if ($this->getMeta("key"))
				$gitHubRepo->setAccessToken($this->getMeta("key"));

			try {
				$issues=$gitHubRepo->getIssues();
			}

			catch (Exception $e) {
				$this->setMeta("lastFetchError",$e->getMessage());				
				return;
			}

			foreach ($issues as $issue) {
				if ($this->filterIssue($issue)) {
					$issue->issueFilterId=$this->getId();
					$issue->save();
					$this->issues[]=$issue;
				}
			}
		}

		$this->setMeta("lastFetch",time());
	}

	/**
	 * Get error for the last fetch, if any.
	 */
	public function getLastError() {
		return $this->getMeta("lastFetchError");
	}

	/**
	 * Filter an issue.
	 */
	public function filterIssue($issue) {
		foreach ($this->getMeta("labels") as $label)
			if (trim($label) && !in_array(trim($label),$issue->getLabels()))
				return FALSE;

		$state=$this->getMeta("state");
		if ($state=="open" || $state=="closed")
			if ($issue->getState()!=$state)
				return FALSE;

		if ($this->getMeta("assigned")=="yes")
			if (!sizeof($issue->getAssigneeLogins()))
				return FALSE;

		if ($this->getMeta("assigned")=="no")
			if (sizeof($issue->getAssigneeLogins()))
				return FALSE;

		if (intval($this->getMeta("createdLastDays"))) {
			$data=$issue->getIssueData();
			$t=strtotime($data["created_at"]);
			if ($t<time()-$this->getMeta("createdLastDays")*60*60*24)
				return FALSE;
		}

		if (intval($this->getMeta("closedLastDays"))) {
			$data=$issue->getIssueData();
			$t=strtotime($data["closed_at"]);
			if ($t<time()-$this->getMeta("closedLastDays")*60*60*24)
				return FALSE;
		}

		if (intval($this->getMeta("updatedLastDays"))) {
			$data=$issue->getIssueData();
			$t=strtotime($data["updated_at"]);
			if ($t<time()-$this->getMeta("updatedLastDays")*60*60*24)
				return FALSE;
		}

		return TRUE;
	}

	/**
	 * Get the list of issues for this filter.
	 * The issues will be cached in the local database for an hour.
	 */
	public function getIssues() {
		if (is_null($this->issues)) {
			if (time()>$this->getMeta("lastFetch")+3600)
				$this->populateIssueCache();

			$this->issues=RepoIssue::findAllBy("issueFilterId",$this->getId());
		}

		return $this->issues;
	}

	/**
	 * Show as?
	 */
	public function getShowAs() {
		return $this->getMeta("show");
	}

	/**
	 * Get number of issues.
	 */
	public function getNumIssues() {
		return sizeof($this->getIssues());
	}

	/**
	 * Same as getNumIssues, but clears the cache first.
	 */
	public function getMeasurement() {
		$this->clearIssueCache();
		return $this->getNumIssues();
	}

	/**
	 * Get title.
	 */
	public function getTitle() {
		return $this->post->post_title;
	}

	/**
	 * The post is saved.
	 */
	public function onSave() {
		$this->clearIssueCache();
	}
}