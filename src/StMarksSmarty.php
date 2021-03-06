<?php

/** StMarksSmarty and related classes */

namespace smtech\StMarksSmarty;

use Battis\BootstrapSmarty\BootstrapSmarty;
use Battis\DataUtilities;

/**
 * A wrapper for Smarty to set (and maintain) defaults
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class StMarksSmarty extends BootstrapSmarty
{
    /**
     * Identifier for UI layer provided by this class
     */
    const KEY = 'StMarks-BootstrapSmarty';

    /**
     * Is this app presented inside of an `iframe`?
     * @var boolean
     */
    private $isFramed = false;

    /**
     * @inheritDoc
     *
     * @param string $template
     * @param string $config
     * @param string $compile
     * @param string $cache
     * @return StMarksSmarty
     */
    public static function getSmarty($template = null, $config = null, $compile = null, $cache = null)
    {
        if (self::$singleton === null) {
            self::$singleton = new self($template, $config, $compile, $cache);
        }
        return self::$singleton;
    }

    /**
     * @inheritDoc
     *
     * @param string $template
     * @param string $config
     * @param string $compile
     * @param string $cache
     */
    public function __construct($template = null, $config = null, $compile = null, $cache = null)
    {
        parent::__construct($template, $config, $compile, $cache);

        $this->prependTemplateDir(__DIR__ . '/../templates', self::KEY);
        $this->addStylesheet(
            DataUtilities::URLfromPath(__DIR__ . '/../css/StMarksSmarty.css'),
            self::KEY
        );
    }

    /**
     * Set whether or not this app is in an `iframe`
     *
     * @param boolean $isFramed
     */
    public function setFramed($isFramed)
    {
        $this->isFramed = (bool) $isFramed;
    }

    /**
     * Is this app presented in an `iframe`?
     *
     * @return boolean
     */
    public function isFramed()
    {
        return $this->isFramed;
    }

    /**
     * @inheritDoc
     *
     * @param string $template
     * @param string $cache_id
     * @param string $compile_id
     * @param string $parent
     * @return void
     */
    public function display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null)
    {
        if ($this->isFramed()) {
            $this->addStylesheet(
                DataUtilities::URLfromPath(__DIR__ . '/../css/StMarksSmarty.css') . '?isFramed=true',
                self::KEY
            );
        }
        $this->assign('isFramed', $this->isFramed());

        parent::display($template, $cache_id, $compile_id, $parent);
    }
}
