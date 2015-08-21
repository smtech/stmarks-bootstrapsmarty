<?php

/** StMarksSmarty and related classes */

/**
 * A wrapper for Smarty to set (and maintain) defaults
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
final class StMarksSmarty extends Smarty {

	/**
	 * @var StMarksSmarty|NULL Reference to the singleton StMarksSmarty
	 *		instance
	 **/
	private static $singleton = null;


	/**
	 * Default key for app-specified entry in lists of template and config
	 * directories
	 **/
	const APP_KEY = 'app';

	/**
	 * Default key for StMarksSmarty-specified entry in lists of template and
	 * config directories
	 **/
	const UI_KEY = 'StMarksSmarty';


	/**
	 * @var string[] Directory used by StMarksSmarty for base
	 *		templates (always included in template directories list)
	 **/
	private $uiTemplateDir = null;
	
	/**
	 * @var string[] Directory used by StMarksSmarty for base configs
	 *		(always included in config directories list)
	 **/
	private $uiConfigDir = null;
	
	/**
	 * @var string Default directory used by StMarksSmarty for
	 *		compiled templates (can be overridden)
	 **/
	private $uiCompileDir = null;
	
	/**
	 * @var string Default directory used by StMarksSmarty for cache
	 *		files (can be overriden)
	 **/
	private $uiCacheDir = null;

	/**
	 * @var NotificationMessage[] List of pending notification messages
	 *		to be displayed
	 **/
	private $messages = array();
	
	/** @var string[] $stylesheets List of stylesheets to be applied */
	private $stylesheets = array();
	
	/**
	 * @var array Application metadata backup (in case
	 *		[battis/appmetadata](https://github.com/battis/appmetadata) is not being
	 *		used
	 **/
	private $minimalMetadata = array();
	
	/**
	 * @var boolean Whether or not this app is displayed within an
	 *		IFRAME, determines base stylesheet (defaults to false)
	 **/
	private $isFramed = false;
	
	/**
	 * Test a file systems directory for writeability by the Apache user
	 *
	 * Note that this method throws an exception _rather than_ returning false, as
	 * no pages can be displayed using the Smarty templating system if the
	 * directories being checked do not exist. An application that was fault-
	 * tolerant enough to work around these missing directories should catch this
	 * exception, rather than expecting a false result.
	 *
	 * @param string $directory
	 *
	 * @return boolean TRUE if the directory is writeable
	 *
	 * @throws StMarksSmarty_Exception UNWRITABLE_DIRECTORY If the directory is not
	 *		writeable
	 **/
	private static function testWriteableDirectory($directory) {
		$success = false;
		if (file_exists($directory)) {
			if (is_dir($directory)) {
				if (is_writable($directory)) {
					$success = true;
				} else {
					$success = chmod($directory, 0775);
				}
			}
		} elseif (!file_exists($directory)) {
			$success = mkdir($directory);
		}
		
		if (!$success) {
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' cannot be created or cannot be made writeable",
				StMarksSmarty_Exception::UNWRITABLE_DIRECTORY
			);
		}
	}
	
	/**
	 * Test a file system directory for readability by the Apache user
	 *
	 * Note that this method throws an exception _rather than_ returning false, as
	 * no pages can be displayed using the Smarty templating system if the
	 * directories being checked do not exist. An application that was fault-
	 * tolerant enough to work around these missing directories should catch this
	 * exception, rather than expecting a false result.
	 *
	 * @param string $directory
	 *
	 * @return boolean TRUE if the directory is writeable
	 *
	 * @throws StMarksSmarty_Exception MISSING_FILES After creating the directory
	 *		(if the directory does not already exist)
	 * @throws StMarksSmarty_Exception UNREADABLE_DIRECTORY If the directory
	 *		exists, but is not readable
	 **/
	private static function testReadableDirectory($directory) {
		$success = false;
		
		if (file_exists($directory)) {
			if (is_dir($directory)) {
				if (is_readable($directory)) {
					$success = true;
				} else {
					$success = chmod($directory, 0555);
				}
			}
		} else {
			/* TODO is this reasonable behavior, or should it simply treat the directory
			   as unreadable? */
			$success = mkdir($directory);
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' was created (should have already existed and been populated)",
				StMarksSmarty_Exception::MISSING_FILES
			);
		}
		
		if (!$success) {
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' is not readable",
				StMarksSmarty_Exception::UNREADABLE_DIRECTORY
			);
		}
	}
	
	/**
	 * Build an array of directories appending the StMarksSmarty defaults
	 *
	 * @param string|string[] $appDir Application directory (or directories,
	 *		optionally with associative array keys for identification)
	 * @param string|string{} $uiDir StMarksSmarty directory defaults
	 * @param boolean $arrayResult (Optional) Whether or not the result should be
	 *		a string or an array of strings (defaults to true, an array of strings)
	 **/
	private static function appendUiDefaults($appDir, $uiDir, $arrayResult = true) {
		
		/* FIXME Currently assumes that $uiDir will always be passed correctly as
		   either a string or an array of strings, but does no checks */
		
		if ($arrayResult) {
			if (!empty($appDir)) {
				if (is_array($appDir)) {
					return array_merge($appDir, $uiDir);
				} else {
					return array_merge(array(self::APP_KEY => $appDir), $uiDir);
				}
			} else {
				return $uiDir;
			}
		} else {
			if (!empty($appDir)) {
				return $appDir;
			} else {
				return $uiDir;
			}
		}
	}
	
	/**
	 * Return the singleton instance of StMarksSmarty
	 *
	 * @param boolean $isFramed Whether the app is displayed within an IFRAME or
	 *		not (defaults to FALSE)
	 * @param string|string[] $template (Optional) Additional Smarty template
	 *		directories
	 * @param string|string[] $config (Optional) Additional Smarty config
	 *		directories
	 * @param string $compile (Optional) Alternative Smarty compiled template
	 *		directory
	 * @param string $cache (Optional) Alternative Smarty cache directory
	 *
	 * @return StMarksSmarty
	 *
	 * @see http://www.phptherightway.com/pages/Design-Patterns.html#singleton Singleton Design Pattern
	 **/
	public static function getSmarty($isFramed = false, $template = null, $config = null, $compile = null, $cache = null) {
		if (self::$singleton === null) {
			self::$singleton = new self($isFramed, $template, $config, $compile, $cache);
		}
		return self::$singleton;
	}
	
	/**
	 * Construct the singleton instance of StMarksSmarty
	 *
	 * @deprecated Use singleton pattern StMarksSmarty::getSmarty()
	 *
	 * @param boolean $isFramed Whether the app is displayed within an IFRAME or
	 *		not (defaults to FALSE)
	 * @param string|string[] $template (Optional) Additional Smarty template
	 *		directories
	 * @param string|string[] $config (Optional) Additional Smarty config
	 *		directories
	 * @param string $compile (Optional) Alternative Smarty compiled template
	 *		directory
	 * @param string $cache (Optional) Alternative Smarty cache directory
	 *
	 * @return void
	 *
	 * @throws StMarksSmarty_Exception SINGLETON If an instance of StMarksSmarty already exists
	 *
	 * @see StMarksSmarty::getSmarty() StMarksSmarty::getSmarty()
	 * @see http://www.phptherightway.com/pages/Design-Patterns.html#singleton Singleton Design Pattern
	 **/
	public function __construct($isFramed = false, $template = null, $config = null, $compile = null, $cache = null) {
		if (self::$singleton !== null) {
			throw new StMarksSmarty_Exception(
				'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
				StMarksSmarty_Exception::SINGLETON
			);
		} else {
			parent::__construct();
			self::$singleton = $this;
		}
		
		/* Default to local directories for use by Smarty */
		$this->uiTemplateDir = array(self::UI_KEY => __DIR__ . '/templates');
		$this->uiConfigDir = array(self::UI_KEY => __DIR__ . '/configs');
		$this->uiCompileDir = __DIR__ . '/templates_c';
		$this->uiCacheDir = __DIR__ . '/cache';
		
		/* Apply user additions and alternates */
		$this->setTemplateDir($template);
		$this->setConfigDir($config);
		$this->setCompileDir($compile);
		$this->setCacheDir($cache);
		
		/* Test all directories for use by Smarty */
		foreach($this->getTemplateDir() as $key => $dir) {
			self::testReadableDirectory($dir);
		}
		foreach($this->getConfigDir() as $key => $dir) {
			self::testReadableDirectory($dir);
		}
		self::testWriteableDirectory($this->getCompileDir());
		self::testWriteableDirectory($this->getCacheDir());
		
		/* Define some preliminary $metadata array values (assuming use of
		   [battis/appmetadata](https://github.com/battis/appmetadata)) used by
		   templates */
		$this->minimalMetadata['APP_URL'] =
			(
				!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ?
					'http://' :
					'https://'
			) .
			$_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', str_replace('/vendor/smtech/stmarkssmarty', '', __DIR__));
		$this->minimalMetadata['APP_NAME'] = 'St. Mark&rsquo;s School';
		
		/* Define base stylesheet */
		$this->setFramed($isFramed);
		$this->stylesheets[self::UI_KEY] = $this->minimalMetadata['APP_URL'] . '/vendor/smtech/stmarkssmarty/css/StMarksSmarty.css?isFramed=' . ($isFramed ? 1 : 0);
		$this->assign('isFramed', ($isFramed ? 1 : 0));
	}
	
	/**
	 * Change any necessary properties after a shallow copy cloning
	 *
	 * @deprecated Use singleton pattern StMarksSmarty::getSmarty()
	 *
	 * @throws StMarksSmarty_Exception SINGLETON If method is invoked.
	 *
	 * @see StMarksSmarty::getSmarty() StMarksSmarty::getSmarty()
	 * @see http://php.net/manual/en/language.oop5.cloning.php#object.clone Object Cloning
	 * @see http://www.phptherightway.com/pages/Design-Patterns.html#singleton Singleton Design Pattern
	 **/
	private function __clone() {
		throw new StMarksSmarty_Exception(
			'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
			StMarksSmarty_Exception::SINGLETON
		);
	}
	
	/**
	 * Reconstruct any resources used by an object upon unserialize()
	 *
	 * @deprecated Use singleton pattern StMarksSmarty::getSmarty()
	 *
	 * @throws StMarksSmarty_Exception SINGLETON If method is invoked.
	 *
	 * @see StMarksSmarty::getSmarty() StMarksSmarty::getSmarty()
	 * @see http://php.net/manual/en/oop4.magic-functions.php The magic functions *__sleep* and *__wakeup*
	 * @see http://www.phptherightway.com/pages/Design-Patterns.html#singleton Singleton Design Pattern
	 **/
	private function __wakeup() {
		throw new StMarksSmarty_Exception(
			'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
			StMarksSmarty_Exception::SINGLETON
		);
	}
	
	/**
	 * Set the directories where templates are stored
	 *
	 * Preserves default StMarksSmarty template directory to allow for extensions
	 * and applications of the base templates by application templates.
	 *
	 * @param string|string[] $template Additional Smarty template directories
	 * @param boolean $isConfig (Optional) Defaults to FALSE, set to TRUE when
	 *		Smarty::setTemplateDir() is aliased by Smarty::setConfigDir()
	 *
	 * @used-by StMarksSmarty::setConfigDir()
	 *
	 * @see http://www.smarty.net/docs/en/api.set.template.dir.tpl Smarty::setTemplateDir()
	 **/
	public function setTemplateDir($template, $isConfig = false) {
		if ($isConfig) {
			return parent::setTemplateDir($template, $isConfig);
		} else {
			return parent::setTemplateDir(self::appendUiDefaults($template, $this->uiTemplateDir));
		}
	}
	
	/**
	 * Set the directories where configs are stored
	 *
	 * Preserves default StMarksSmarty config directory to allow for extensions
	 * and applications of the base configs by application configs.
	 *
	 * @param string|string[] $config Additional Smarty config directories
	 *
	 * @uses StMarksSmarty::setTemplateDir() to effect directory-mapping
	 *
	 * @see http://www.smarty.net/docs/en/api.set.config.dir.tpl Smarty::setConfigDir()
	 **/
	public function setConfigDir($config) {
		return parent::setConfigDir(self::appendUiDefaults($config, $this->uiConfigDir));
	}
	
	/**
	 * Set the directory where compiled templates are stored
	 *
	 * Allows $compile to be empty (in which case StMarkSmarty::$uiCompileDir 
	 * default is substituted for the empty value)
	 *
	 * @param string $compile Alternative Smarty compiled template directory
	 *
	 * @see http://www.smarty.net/docs/en/api.set.compile.dir.tpl Smarty::setCompileDir()
	 **/
	public function setCompileDir($compile) {
		return parent::setCompileDir(self::appendUiDefaults($compile, $this->uiCompileDir, false));
	}

	/**
	 * Set the directory where cache files are stored
	 *
	 * Allows $cache to be empty (in which case StMarksSmarty::$uiCacheDir is
	 * substituted for the empty value)
	 *
	 * @param string $cache Alternative Smarty cache file directory
	 *
	 * @see http://www.smarty.net/docs/en/api.set.cache.dir.tpl Smarty::setCacheDir()
	 **/
	public function setCacheDir($cache) {
		return parent::setCacheDir(self::appendUiDefaults($cache, $this->uiCacheDir, false));
	}
	
	public function addTemplateDir($template, $key = null, $isConfig = false) {
		if ($isConfig) {
			return parent::addTemplateDir($template, $key, $isConfig);
		} else {
			if (!empty($key) && !empty($this->getTemplateDir($key))) {
				return parent::addTemplateDir($template, $key);
			} else {
				if (!empty($key)) {
					$template = array($key => $template);
				}
				return parent::setTemplateDir(self::appendUiDefaults($template, $this->getTemplateDir()));
			}
		}
	}

	public function addConfigDir($config, $key = null) {
		if (!empty($key) && !empty($this->getConfigDir($key))) {
			return parent::addConfigDir($template, $key);
		} else {
			if (!empty($key)) {
				$config = array($key => $config);
			}
			return parent::setConfigDir($self::appendUiDefaults($config, $this->getConfigDir()));
		}
	}
	
	/**
	 * Add additional CSS stylesheets
	 *
	 * Additional stylesheets are loaded after the base stylesheet(s)
	 *
	 * @param string|string[] $stylesheet URL(s) of additional stylesheets (with
	 *		optional associative array keys naming them)
	 * @param string $key (Optional) Identifying key for a single stylesheet
	 *		(Applied with numeric identifiers if $stylesheet is an array without its
	 *		own defined associative array keys). If $key already exists in the list of
	 *		stylesheets, that stylesheet is replaced by $stylesheet
	 *
	 * @throws StMarksSmarty_Exception NOT_A_URL If $stylesheet is not a URL or an
	 *		array of URLs
	 **/
	public function addStylesheet($stylesheet, $key = null) {
		/* default to the APP_KEY if no key is set */
		$_key = self::APP_KEY;
		if (!empty($key)) {
			$_key = $key;
		}
		
		/* construct the array of additional stylesheets */
		$_stylesheet = array();
		/* Is $stylesheet an associative array? If so, just assume that the user knows
		   what they're doing (names, no names, whatevs).
		   http://stackoverflow.com/a/4254008/294171 */
		if (is_array($stylesheet) && count(array_filter(array_keys($stylesheet), 'is_string'))) {
			// FIXME actually test the array elements to see if they are URLs
			$_stylesheet = $stylesheet;
		} elseif (is_array($stylesheet)) { /* non-associative array */
			/* continue auto-numbering already started for this key */
			$counter = 1;
			foreach (array_keys($this->stylesheets) as $name => $s) {
				if (preg_match("/$_key-(\d+)/", $name, $match)) {
					$counter = max($counter, $match[1] + 1);
				}
			}
			foreach ($stylesheet as $s) {
				if (is_string($s)) {
					$_stylesheet["{$_key}-{$counter}"] = $s;
					$counter++;
				} else {
					throw new StMarksSmarty_Exception(
						"'{$s}' is not a URL to a CSS stylesheet",
						StMarksSmarty_Exception::NOT_A_URL
					);
				}
			}
		} elseif (is_string($stylesheet)) { /* single stylesheet url */
			$_stylesheet[$_key] = $stylesheet;
		} else {
			throw new StMarksSmarty_Exception(
				"'$stylesheet' is not a URL to a CSS stylesheet",
				StMarksSmarty_Exception::NOT_A_URL
			);
		}
		
		/* append or replace (if $key is not empty) stylesheets */
		$this->stylesheets = array_replace($this->stylesheets, $_stylesheet);
	}
	
	/**
	 * Set whether app is displayed within an IFRAME or not
	 *
	 * @param boolean $isFramed
	 *
	 * @return void
	 **/
	public function setFramed($isFramed) {
		$this->isFramed = ($isFramed == true);
	}
	
	/**
	 * Is this app displayed within an IFRAME?
	 *
	 * @return boolean
	 **/
	public function isFramed() {
		return $this->isFramed();
	}
	
	/**
	 * Return list of stylesheets, optionally matching $key
	 *
	 * If $key is empty, all stylesheets are returned.
	 *
	 * If $key is non-empty, both stylesheets matching $key exactly and stylesheets
	 * matching $key-##, where ## is an auto-generated numeric index, will be
	 * returned.
	 *
	 * @param string $key Name of stylesheet(s) to return
	 *
	 * @return string[] List of stylesheets matching $key
	 **/
	public function getStylesheet($key = null) {
		if (empty($key)) {
			return $this->stylesheets;
		} else {
			$result = array();
			foreach($this-stylesheets as $name => $value) {
				if ($name == $key) {
					$result[$key] = $value;
				} elseif (preg_match("/$key-\d+/", $name)) {
					$result[$name] = $value;
				}
			}
			return $result;
		}
	}
	
	/**
	 * Add a message to be diplayed to the user
	 *
	 * @param string $title HTML-formatted title of the message
	 * @param string $content HTML-formatted content of the message
	 * @param string $class (Optional) CSS class name of the message ("message is
	 *		default value, "error" and "good" are also styled by default)
	 **/
	public function addMessage($title, $content, $class = 'message') {
		$this->messages[] = new NotificationMessage($title, $content, $class);
	}

	/**
	 * Displays the template
	 *
	 * Overrides Smarty::display() to provide some built-in template variables,
	 * including stylesheets, messages and
	 * [battis/appmetadata](https://github.com/battis/appmetadata), if present.
	 *
	 * @param string $template (Optional) Name of template file (defaults to
	 *		'page.tpl')
	 * @param string $cache_id (Optional)
	 * @param string $compile_id (Optional)
	 * @param string $parent (Optional)
	 *
	 * @see http://www.smarty.net/docs/en/api.display.tpl Smarty::display()
	 **/
	public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
		global $metadata; // FIXME grown-ups don't program like this
		if (empty($metadata)) {
			$this->assign('metadata', $this->minimalMetadata);
		} else {
			$this->assign('metadata', array_replace($this->minimalMetadata, $metadata->getArrayCopy()));
		}
		$this->assign('uiMessages', $this->messages);
		$this->assign('uiStylesheets', $this->stylesheets);
		parent::display($template, $cache_id, $compile_id, $parent);
	}
}

/**
 * All exceptions thrown by StMarkSmarty
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class StMarksSmarty_Exception extends Exception {
	/** Violation of singleton design pattern */
	const SINGLETON = 1;
	
	/** A directory that needs to be readable is not */
	const UNREADABLE_DIRECTORY = 2;
	
	/** A directory that needs to be writable is not */
	const UNWRITABLE_DIRECTORY = 3;
	
	/** A file or directory that should exist does not */
	const MISSING_FILES = 4;
	
	/** A URL was expected, but not received */
	const NOT_A_URL = 5;
}
	
?>