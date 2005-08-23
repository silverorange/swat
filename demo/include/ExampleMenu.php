<?php

class ExampleMenu
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
			'MessageBox' =>    'SwatMessageBox',
			'Pagination' =>    'SwatPagination',
			'PasswordEntry' => 'SwatPasswordEntry',
			'RadioList' =>     'SwatRadioList',
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
