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
	}
}