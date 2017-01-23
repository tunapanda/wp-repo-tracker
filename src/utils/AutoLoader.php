<?php

namespace repotracker;

use \Exception;

/**
 * Manage auto loading using spl_autoload_register
 */
class AutoLoader {

	private $namespace;
	private $sourcePaths;

	/**
	 * Constructor
	 */
	public function __construct($namespace=NULL) {
		$this->namespace=NULL;
		if ($namespace)
			$this->setNamespace($namespace);

		$this->sourcePaths=array();
	}

	/**
	 * Set namespace for which we are responsible.
	 */
	public function setNamespace($namespace) {
		$this->namespace=$namespace;
	}

	/**
	 * Add a source path where to look for sources.
	 */
	public function addSourcePath($sourcePath) {
		$this->sourcePaths[]=$sourcePath;
	}

	/**
	 * Add all sub folders as source paths.
	 */
	public function addSourceTree($sourceTree) {
		foreach (scandir($sourceTree) as $dirName)
			if (is_dir($sourceTree."/".$dirName) && substr($dirName,0,1)!=".")
				$this->addSourcePath($sourceTree."/".$dirName);
	}

	/**
	 * Register the auto loader.
	 */
	public function register() {
		spl_autoload_register(array($this,"autoloader"));
	}

	/**
	 * The function that is registered to handle autoloading
	 * for the system.
	 */
	public function autoloader($fullClassName) {
		if (!$this->namespace)
			throw new Exception("Namespace not set");

		if (!$this->sourcePaths)
			throw new Exception("There are no source paths added");

		$namespacePart=$this->namespace."\\";
		if (substr($fullClassName,0,strlen($namespacePart))!=$namespacePart)
			return;

		$className=substr($fullClassName,strlen($namespacePart));

		foreach ($this->sourcePaths as $sourcePath) {
			$classFileName=$sourcePath."/".$className.".php";
			if (file_exists($classFileName))
				require_once $classFileName;
		}
	}
}