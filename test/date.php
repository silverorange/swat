<?php

require('header.php');

require_once('Swat/SwatLayout.php');

$layout = new SwatLayout('date.xml');

// TODO: not sure about this notation:
$form1 = $layout->getWidget('form1');
$frame1 = $layout->getWidget('frame1');

$date = $layout->getWidget('date');
$date->display = (SwatDate::YEAR | SwatDate::MONTH | SwatDate::DAY);

if ($form1->process()) {
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
}

$frame1->displayTidy();
//$frame1->display();

require('footer.php');
?>

