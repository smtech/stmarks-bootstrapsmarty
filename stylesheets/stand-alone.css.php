<?php

require_once(__DIR__ . '/../../../autoload.php');
use smtech\StMarksColors;

?>

body {
	padding-top: 70px;
}

#header {
	height: 70px;
	background: url('../images/stand-alone/header.jpg') repeat-x left center;
}

#header-logo {
	background: url('../images/stand-alone/logo.png') no-repeat;
	background-position: 0px 0px;
	height: 70px;
	width: 250px;
}

h1, h2, h3, h4, h5, h6 {
	color: <?= StMarksColors::STMARKS_BLUE ?>;
}