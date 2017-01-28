<?php

namespace repotracker;

/**
 * The main plugin.
 */
class RepoTrackerPlugin extends Singleton {

	/**
	 * Constructor.
	 */
	public function __construct() {
		IssueFilterController::instance();

		$pluginFileName=$this->getPluginFileName();
		register_activation_hook($pluginFileName,array($this,"activate"));
		register_uninstall_hook($pluginFileName,array($this,"uninstall"));

		//register_deactivation_hook($pluginFileName,array($this,"uninstall"));

		IssueFilter::register();

		add_filter("register_kpis",array($this,"registerKpis"));
	}

	/**
	 * Get plugin filename.
	 */
	public function getPluginFileName() {
		return REPOTRACKER_PATH."/wp-repo-tracker.php";
	}

	/**
	 * Plugin activation.
	 */
	public function activate() {
		RepoIssue::install();
	}

	/**
	 * Plugin removal.
	 */
	public function uninstall() {
		RepoIssue::uninstall();
	}

	/**
	 * Register kpis for the wp-data-kpis plugin.
	 */
	public function registerKpis($kpis) {
		$issueFitlers=IssueFilter::getAllPublished();

		foreach ($issueFitlers as $issueFilter) {
			$kpis[$issueFilter->getPost()->post_name]=array(
				"title"=>$issueFilter->getPost()->post_title
			);
		}

		return $kpis;
	}
}