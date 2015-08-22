<?php
/** NotificationMessage and related classes */

/**
 * A notification message for the user
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class NotificationMessage {

	/** Default CSS class selector for message */
	const INFO = 'alert-info';
		
	/**
	 * Alias for INFO
	 *
	 * @deprecated Use `INFO` instead for consistency with Bootstrap
	 **/
	const MESSAGE = 'alert-info';
	
	/** CSS class selector for an error message */
	const DANGER = 'alert-danger';
	
	/**
	 * Alias for DANGER
	 *
	 * @deprecated Use `DANGER` instead for consistency with Bootstrap
	 **/
	const ERROR = 'alert-danger';
	
	/** CSS class selector for a "good" (indiciating successful operation) message */
	const SUCCESS = 'alert-success';
	
	/**
	 * Alias for SUCCESS
	 *
	 * @deprecated Use `SUCCESS` instead for consistency with Bootstrap
	 **/
	const GOOD = 'alert-success';
	
	/** CSS class selector for an important message */
	const WARNING = 'alert-warning';
	
	/** @var string $title HTML-formatted title of the message */
	public $title = null;
	
	/** @var string $content HTML-formatted content of the message */
	public $content = null;
	
	/** @var string $class CSS class selector of the message */
	public $class = self::MESSAGE;
	
	/**
	 * Construct a new notification message
	 *
	 * @param string $title
	 * @param string $content
	 * @param string $class (Optional) Defaults to NotificationMessage::MESSAGE
	 *
	 * @return void
	 **/
	public function __construct($title, $content, $class = self::INFO) {
		$this->title = $title;
		$this->content = $content;
		$this->class = $class;
	}
}
	
?>