<?php
//setlocale(LC_TIME, "fr_FR");
require('header.php');

require_once('Swat/SwatLayout.php');

$layout = new SwatLayout('date.xml');

// TODO: not sure about this notation:
$form1 = $layout->getWidget('form1');
$frame1 = $layout->getWidget('frame1');

$date = $layout->getWidget('date2');
//$date->display = (SwatDate::YEAR | SwatDate::MONTH | SwatDate::CALENDAR);

/*
$date->valid_range_start = new Date();
$date->valid_range_start->subtractSeconds(86400*1456);
$date->valid_range_end = clone $date->valid_range_start;
$date->valid_range_end->addSeconds(86400*200);
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

