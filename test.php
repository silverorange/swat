<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Swat Example Form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css" media="all">@import "Swat/css/swat.css";</style> 
</head>

<body>
<?

ini_set('include_path', '/so/include');
require_once('Swat/SwatForm.php');
require_once('Swat/SwatFrame.php');
require_once('Swat/SwatEntry.php');
require_once('Swat/SwatTextarea.php');
require_once('Swat/SwatCheckbox.php');
require_once('Swat/SwatButton.php');
require_once('Swat/SwatFlydown.php');
require_once('Swat/SwatFormField.php');
require_once('Swat/SwatDiv.php');


// create the top-level widget
$frame = new SwatFrame('frame1');
$frame->title = 'New Weblog Post Example Form';

// create a form widget and add it to the frame
$form = new SwatForm('form1');
$frame->add($form);

// create an entry widget and add it
$title = new SwatEntry('title');
$title->required = true;
$form->addWithField($title, 'Title:');

// create an entry widget and add it
/*
$field = new SwatFormField();
$entry = new SwatEntry('entry1');
$entry->required = true;
$field->add($entry);
$field->title = "First Name:";
$form->add($field);
*/

// create a checkbox widget and add it
$hidden = new SwatCheckbox('hidden');
$form->addWithField($hidden, 'Hidden?:');

// create a textarea widget and add it
$bodytext = new SwatTextarea('bodytext');
$bodytext->required = true;
$form->addWithField($bodytext, 'Body Text:');

// create a textarea widget and add it
$moretext = new SwatTextarea('moretext');
$form->addWithField($moretext, 'More Text:');

// create a flydown widget and add it
$fly = new SwatFlydown('replystatus');
$fly->options = array(0 => 'Normal', 1 => 'Hidden');
$fly->selected_value = 0;
$form->addWithField($fly, 'Reply Status:');

// create a checkbox widget and add it
$ping = new SwatCheckbox('ping');
$form->addWithField($ping, 'Ping Weblogs.com?:');

// create a button widget and add it
$div = new SwatDiv();
$btn = new SwatButton('btn_create');
$btn->title = 'Create';
$form->addWithDiv($btn, 'SwatFormFooter');

if ($form->process()) {
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	//echo $entry->text, "\n";
}

$frame->displayTest();
?>
</body>
</html>

