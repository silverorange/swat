<?php

require('header.php');

require_once('Swat/SwatLayout.php');

$layout = new SwatLayout('edit.xml');

// TODO: not sure about this notation:
$replystatus = $layout->getWidget('replystatus');
$form1 = $layout->getWidget('form1');
$frame1 = $layout->getWidget('frame1');

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

