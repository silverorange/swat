<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Swat Example Form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css" media="all">@import "../Swat/css/swat.css";</style>
</head>
<body>
<?

#ini_set('include_path', '/so/include');
ini_set('include_path', '..');
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

$frame1->displayTest();

?>
</body>
</html>

