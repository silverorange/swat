<?php

require('header.php');

require_once('Swat/SwatLayout.php');

$layout = new SwatLayout('date.xml');

// TODO: not sure about this notation:
$form1 = $layout->getWidget('form1');
$frame1 = $layout->getWidget('frame1');

$date = $layout->getWidget('date');
$date->display = (SwatDate::YEAR | SwatDate::MONTH | SwatDate::DAY | SwatDate::TIME);

/*
$date->valid_range_start = new Date();
$date->valid_range_start->subtractSeconds(86400*2000);
$date->valid_range_end = clone $date->valid_range_start;
$date->valid_range_end->addSeconds(86400*5);
*/

/*
$date->valid_range_end->setYear(2005);
$date->valid_range_end->setMonth(3);
$date->valid_range_end->setDay(23);
*/

/*
$time = $layout->getWidget('time');
$time->valid_range_start = new Date();
$time->valid_range_end = clone $date->valid_range_start;
*/

if ($form1->process()) {
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
}

$frame1->displayTidy();
//$frame1->display();

require('footer.php');
?>

