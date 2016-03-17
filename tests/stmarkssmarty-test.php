<?php

require_once('common.inc.php');

use smtech\StMarksSmarty\StMarksSmarty;
use Battis\BootstrapSmarty\NotificationMessage;

$ui = StMarksSmarty::getSmarty();
$ui->addTemplateDir(__DIR__ .'/templates');

$ui->addMessage('foo', '<a href="#">link1</a> <a class="test" href="#">link2</a> not link', NotificationMessage::ERROR);
$ui->addMessage('foo', 'bar', NotificationMessage::GOOD);
$ui->addMessage('<a href="#">link</a> not link', 'another message');

$ui->assign('formName', 'Sample Form');
$ui->assign('formAction', $_SERVER['PHP_SELF']);

$ui->setFramed(false);
$ui->enable(StMarksSmarty::MODULE_DATEPICKER);

$ui->setFramed(true);

$ui->display("form-page.tpl");
	
?>