<?php
/*
Plugin Name: Repository Tracker
Plugin URI: https://github.com/tunapanda/wp-repo-tracker
GitHub Plugin URI: https://github.com/tunapanda/wp-repo-tracker
Description: Track issues from a code repository, and show them on your WordPress site.
Version: 0.0.3
*/

define('REPOTRACKER_PATH',plugin_dir_path(__FILE__));
define('REPOTRACKER_URL',plugins_url('',__FILE__));

if (!defined("RWMB_URL")) {
	define("RWMB_URL",REPOTRACKER_URL."/ext/meta-box/");
	require_once __DIR__."/ext/meta-box/meta-box.php";
}

require_once __DIR__."/src/utils/AutoLoader.php";

$autoLoader=new repotracker\AutoLoader("repotracker");
$autoLoader->addSourceTree(__DIR__."/src");
$autoLoader->register();

$autoLoader=new repotracker\AutoLoader();
$autoLoader->addSourcePath(__DIR__."/ext/wprecord");
$autoLoader->register();

repotracker\RepoTrackerPlugin::instance();