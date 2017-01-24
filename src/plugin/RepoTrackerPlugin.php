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

}