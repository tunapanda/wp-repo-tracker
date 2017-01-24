<?php

namespace repotracker;

/**
 * Wraps the issuefilter post type.
 * TODO:
 * x state
 * - assigned
 * - createdLastDays
 * - updatedLastDays
 * - closedLastDays
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
	}

	/**
	 * Populate issue cache.
	 */
	public function populateIssueCache() {
		$this->clearIssueCache();

		$this->issues=array();
		foreach ($this->getMeta("repositories") as $repoUrl) {
			$gitHubRepo=new GitHubRepo($repoUrl);
			$issues=$gitHubRepo->getIssues();

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

		return TRUE;
	}

	/**
	 * Get the list of issues for this filter.
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
	 * Get number of issues.
	 */
	public function getNumIssues() {
		return sizeof($this->getIssues());
	}

	/**
	 * The post is saved.
	 */
	public function onSave() {
		$this->clearIssueCache();
	}
}