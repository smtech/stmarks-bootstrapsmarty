<?php

final class StMarksSmarty extends Smarty {

	private static $singleton = null;

	const APP_KEY = 'app';
	const UI_KEY = 'StMarksSmarty';

	private $uiTemplateDir = null;
	private $uiConfigDir = null;
	private $uiCompileDir = null;
	private $uiCacheDir = null;

	private $messages = array();
	private $stylesheets = array();
	
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
	
	private static function appendUiDefaults($appDir, $uiDir, $arrayResult = true) {
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
	 * singleton
	 *
	 * @param string|string[] $template (Optional)
	 * @param string|string[] $config (Optional)
	 * @param string $compile (Optional)
	 * @param string $cache (Optional)
	 *
	 * @return StMarksSmarty
	 **/
	public static function getSmarty($template = null, $config = null, $compile = null, $cache = null) {
		if (self::$singleton === null) {
			self::$singleton = new self($template, $config, $compile, $cache);
		}
		return self::$singleton;
	}
	
	/**
	 * singleton
	 *
	 * @param string|string[] $template (Optional)
	 * @param string|string[] $config (Optional)
	 * @param string $compile (Optional)
	 * @param string $cache (Optional)
	 *
	 * @return void
	 **/
	public function __construct($template = null, $config = null, $compile = null, $cache = null) {
		if (self::$singleton !== null) {
			throw new StMarksSmarty_Exception(
				'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
				StMarksSmarty_Exception::SINGLETON
			);
		} else {
			parent::__construct();
			self::$singleton = $this;
		}
		
		$this->uiTemplateDir = array(self::UI_KEY => __DIR__ . '/templates');
		$this->uiConfigDir = array(self::UI_KEY => __DIR__ . '/configs');
		$this->uiCompileDir = __DIR__ . '/templates_c';
		$this->uiCacheDir = __DIR__ . '/cache';
		
		$this->setTemplateDir($template);
		$this->setConfigDir($config);
		$this->setCompileDir($compile);
		$this->setCacheDir($cache);
		
		foreach($this->getTemplateDir() as $key => $dir) {
			self::testReadableDirectory($dir);
		}
		
		foreach($this->getConfigDir() as $key => $dir) {
			self::testReadableDirectory($dir);
		}
		
		self::testWriteableDirectory($this->getCompileDir());
		self::testWriteableDirectory($this->getCacheDir());
				
		// FIXME ...wow. Just... wow.
		$preliminaryMetadata = array(
			'APP_URL' => (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', str_replace('/vendor/smtech/stmarkssmarty', '', __DIR__)),
			'APP_NAME' => 'St. Mark&rsquo;s School'
		);
		$this->assign('metadata', $preliminaryMetadata);
		
		$this->stylesheets[self::UI_KEY] = $preliminaryMetadata['APP_URL'] . '/vendor/smtech/stmarkssmarty/stylesheets/stylesheet.css';
		$this->assign('stylesheets', $this->stylesheets);
	}
	
	/** singleton */
	private function __clone() {
		throw new StMarksSmarty_Exception(
			'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
			StMarksSmarty_Exception::SINGLETON
		);
	}
	
	/** singleton */
	private function __wakeup() {
		throw new StMarksSmarty_Exception(
			'StMarksSmarty is a singleton class, use the factory method StMarksSmarty::getSmarty() instead of ' . __METHOD__,
			StMarksSmarty_Exception::SINGLETON
		);
	}
	
	/** avoid losing the UI templates */
	public function setTemplateDir($template, $isConfig = false) {
		if ($isConfig) {
			return parent::setTemplateDir($template, $isConfig);
		} else {
			return parent::setTemplateDir(self::appendUiDefaults($template, $this->uiTemplateDir));
		}
	}
	
	/** avoid losing the UI configs */
	public function setConfigDir($config) {
		return parent::setConfigDir(self::appendUiDefaults($config, $this->uiConfigDir));
	}
	
	public function setCompileDir($compile) {
		return parent::setCompileDir(self::appendUiDefaults($compile, $this->uiCompileDir, false));
	}
	
	public function setCacheDir($cache) {
		return parent::setCacheDir(self::appendUiDefaults($cache, $this->uiCacheDir, false));
	}
	
	/** add a new stylesheet */
	public function addStylesheet($stylesheet, $key = null) {
		if (empty($key)) {
			$this->stylesheets[] = $stylesheet;
		} else {
			$this->stylesheets[$key] = $stylesheet;
		}
	}
	
	public function addMessage($title, $content, $class = 'message') {
		$this->messages[] = new NotificationMessage($title, $content, $class);
	}
	
	public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
		$this->assign('uiMessages', $this->messages);
		$this->assign('uiStylesheets', $this->stylesheets);
		parent::display($template, $cache_id, $compile_id, $parent);
	}
}

class StMarksSmarty_Exception extends Exception {
	const SINGLETON = 1;
	const UNREADABLE_DIRECTORY = 2;
	const UNWRITABLE_DIRECTORY = 3;
	const MISSING_FILES = 4;
}
	
?>