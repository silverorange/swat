<?php

require('header.php');

require_once('Swat/SwatUI.php');

$interface = new SwatUI();
$interface->loadFromXML('edit.xml');

// TODO: not sure about this notation:
$replystatus = $interface->getWidget('replystatus');
$form1 = $interface->getWidget('form1');
$frame1 = $interface->getWidget('frame1');

$replystatus->options = array('0' => 'Normal', '1' => 'Hidden');
$replystatis->selected_value = '0';

if ($form1->process()) {
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
}

$frame1->displayTidy();
//$frame1->display();

require('footer.php');
?>

