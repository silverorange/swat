<?php

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoMenu
{
	public function display()
	{
		$demos = array(
			'Calendar' =>      'SwatCalendar',
			'ChangeOrder' =>   'SwatChangeOrder',
			'Checkbox' =>      'SwatCheckbox',
			'ColorEntry' =>    'SwatColorEntry',
			'DateEntry' =>     'SwatDateEntry',
			'Disclosure' =>    'SwatDisclosure',
			'Entry' =>         'SwatEntry',
			'Fieldset' =>      'SwatFieldset',
			'FileEntry' =>     'SwatFileEntry',
			'Flydown' =>       'SwatFlydown',
			'Frame' =>         'SwatFrame',
			'MessageDisplay' =>'SwatMessageDisplay',
			'Pagination' =>    'SwatPagination',
			'PasswordEntry' => 'SwatPasswordEntry',
			'RadioList' =>     'SwatRadioList',
			'StringDemo' =>    'SwatString',
			'Textarea' =>      'SwatTextarea',
			'TimeZoneEntry' => 'SwatTimeZoneEntry',
			'ToolLink' =>      'SwatToolLink',
			'YesNoFlydown' =>  'SwatYesNoFlydown'
		);
		
		echo '<ul>';

		foreach ($demos as $demo => $title) {
			echo '<li><a href="index.php?demo='.$demo.'">'.$title.'</a></li>';
		}

		echo '</ul>';
	}
}

?>
