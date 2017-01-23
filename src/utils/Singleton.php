<?php

namespace repotracker;

/**
 * Singleton.
 */
abstract class Singleton {
	private static $instances=array();

	/**
	 * Get the singleton instance.
	 */
	public static function instance() {
		$class=get_called_class();

		if (!isset(self::$instances[$class]))
			self::$instances[$class]=new $class;

		return self::$instances[$class];
	}
}