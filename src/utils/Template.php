<?php

namespace repotracker;

/**
 * Simple template renderer.
 */
class Template {

	/**
	 * Constructor.
	 */
	public function __construct($fileName) {
		$this->fileName=$fileName;
	}

	/**
	 * Render the template to a string.
	 */
	public function render($vars=array()) {
		foreach ($vars as $key=>$value)
			$$key=$value;

		ob_start();
		require $this->fileName;
		return ob_get_clean();
	}

	/**
	 * Display the rendered template.
	 */
	public function display($vars=array()) {
		if (!$vars)
			$vars=array();

		foreach ($vars as $key=>$value)
			$$key=$value;

		require $this->fileName;
	}

	/**
	 * Render html for a list of options.
	 */
	public static function options($options, $selected) {
		foreach ($options as $key=>$value) {
			printf(
				"<option value='%s' %s>%s</option>",
				htmlspecialchars($key),
				($key==$selected?"selected":""),
				htmlspecialchars($value)
			);
		}
	}
}
