<?php

final class StMarksSmarty extends Smarty {

	private static $singleton = null;

	const APP_KEY = 'app';
	const ENGINE_KEY = 'engine';

	private $messages = array();
	
	private static function testWriteableDirectory($directory) {
		$success = false;
		if (file_exists($directory) && is_dir($directory) && !is_writable($directory)) {
			$success = chmod($directory, 0775);
		} elseif (!file_exists($directory) {
			$succes = mkdir($directory);
		}
		
		if (!$succes) {
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' cannot be created or cannot be made writeable",
				StMarksSmarty_Exception::UNWRITABLE_DIRECTORY
			);
		}
	}
	
	private static function testReadableDirectory($directory) {
		$success = false;
		
		if (file_exists($directory)) {
			if (is_dir($directory) && !is_readable($directory)) {
				$success = chmod($directory, 0555);
			}
		} else {
			$success = mkdir($directory);
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' was created (should have already existed and been populated)",
				StMarksSmarty_Exception::MISSING_FILES
			)
		}
		
		if (!$success)) {
			throw new StMarksSmarty_Exception(
				"The directory '{$directory}' is not readable",
				StMarksSmarty_Exception::UNREADABLE_DIRECTORY
			);
		}
	}
	
	private static function directoryArrayMerge($appDir, $engineDir, $arrayResult = true) {
		if ($arrayResult) {
			if (!empty($appDir)) {
				if (is_array($appDir)) {
					return array_merge($appDir, $engineDir);
				} else {
					return array_merge(array(self::APP_KEY => $appDir), $engineDir);
				}
			} else {
				return $engineDir;
			}
		} else {
			if (!empty($appDir)) {
				return $appDir;
			} else {
				return $engineDir;
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
	private function __construct($template = null, $config = null, $compile = null, $cache = null) {
		parent::__construct();
		
		$engineTemplateDir = array(self::ENGINE_KEY => realpath(__DIR__ . '/templates'));
		$engineConfigDir = array(self::ENGINE_KEY => realpath(__DIR__ . '/configs'));
		$engineCompileDir = realpath(__DIR__ . '/templates_c');
		$engineCacheDir = realpath(__DIR__ . '/cache');
		
		$this->setTemplateDir(self::directoryArrayMerge($template, $engineTemplateDir));
		$this->setConfigDir(self::directoryArrayMerge($config, $engineConfigDir));
		$this->setCompileDir(self::directoryArrayMerge($compile, $engineCompileDir, false));
		$this->setCacheDir(self::directoryArrayMerge($cache, $engineCacheDir, false));
		
		foreach($this->getTemplateDir() as $templateDir) {
			self::testReadableDirectory($templateDir);
		}
		
		foreach($this->getConfigDir() as $configDir) {
			self::testReadableDirectory($configDir);
		}
		
		self::testWriteableDirectory($this->getCompileDir());
		self::testWriteableDirectory($this->getCacheDir());
				
		// FIXME ...wow. Just... wow.
		$preliminaryMetadata = array(
			'APP_URL' => (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', str_replace('/vendor/smtech/stmarkssmarty', '', __DIR__)),
			'APP_NAME' => 'St. Mark&rsquo;s School'
		);
		$this->assign('metadata', $preliminaryMetadata);
	}
	
	/** singleton */
	private function __clone() {}
	
	/** singleton */
	private function __wakeup() {}
	
	public function addMessage($title, $content, $class = 'message') {
		$this->messages[] = new NotificationMessage($title, $content, $class);
	}
	
	public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
		$this->assign('messages', $this->messages);
		parent::display($template, $cache_id, $compile_id, $parent);
	}
}

class StMarksSmarty_Exception extends Exception {
	const UNREADABLE_DIRECTORY = 1;
	const UNWRITABLE_DIRECTORY = 2;
	const MISSING_FILES = 3;
}
	
?>