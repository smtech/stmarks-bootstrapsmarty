<?php

/** StMarksSmarty and related classes */

namespace smtech\StMarksSmarty;

/**
 * A wrapper for Smarty to set (and maintain) defaults
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class StMarksSmarty extends \Battis\BootstrapSmarty\BootstrapSmarty {
	
	private $isFramed = false;
	
	public function __construct($template = null, $config = null, $compile = null, $cache = null) {
		parent::__construct($template, $config, $compile, $cache);
		
		$this->addTemplateDir(__DIR__ . '/../templates');
		$this->addStylesheet(__DIR__ . '/../css/StMarksSmarty.css');
	}
	
	public function setFramed($isFramed) {
		$this->isFramed = (bool) $isFramed;
	}
	
	public function isFramed() {
		return $this->isFramed;
	}
	
	public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
		if (isset($GLOBALS['metadata'])) {
			if (!isset($GLOBALS['metadata']['APP_URL'])) {
				$GLOBALS['metadata']['APP_URL'] =
				(
					!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ?
						'http://' :
						'https://'
				) .
				$_SERVER['SERVER_NAME'] . preg_replace("|^{$_SERVER['DOCUMENT_ROOT']}(.*)$|", '$1', str_replace('/vendor/smtech/stmarkssmarty', '', __DIR__));
			}
			if (!isset($GLOBALS['metadata']['APP_NAME'])) {
				$GLOBALS['metadata']['APP_NAME'] = 'St. Mark&rsquo;s School';
			}
			$this->assign('metadata', $GLOBALS['metadata']);
		}
		
		if ($this->isFramed()) {
			$this->addStylesheet(__DIR__ . '/../css/framed.css');
		}
		$this->assign('isFramed', $this->isFramed());
		
		parent::display_debug($template, $cache_id, $compile_id, $parent);
	}
}

/**
 * All exceptions thrown by StMarkSmarty
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class StMarksSmarty_Exception extends \Battis\BootstrapSmarty\BootstrapSmarty_Exception {
}
	
?>