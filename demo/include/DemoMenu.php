<?php

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoMenu
{
	// {{{ protected properties

	protected $entries = array(
		'Button'            => 'SwatButton',
		'Calendar'          => 'SwatCalendar',
		'ChangeOrder'       => 'SwatChangeOrder',
		'Checkbox'          => 'SwatCheckbox',
		'ColorEntry'        => 'SwatColorEntry',
		'DateEntry'         => 'SwatDateEntry',
		'DetailsView'       => 'SwatDetailsView',
		'Disclosure'        => 'SwatDisclosure',
		'Entry'             => 'SwatEntry',
		'Fieldset'          => 'SwatFieldset',
		'FileEntry'         => 'SwatFileEntry',
		'Flydown'           => 'SwatFlydown',
		'Frame'             => 'SwatFrame',
		'MessageDisplay'    => 'SwatMessageDisplay',
		'NavBar'            => 'SwatNavBar',
		'Pagination'        => 'SwatPagination',
		'PasswordEntry'     => 'SwatPasswordEntry',
		'RadioList'         => 'SwatRadioList',
		'Replicable'        => 'SwatReplicable',
		'StringDemo'        => 'SwatString',
		'TableView'         => 'SwatTableView',
		'TableViewInputRow' => 'SwatTableViewInputRow',
		'Textarea'          => 'SwatTextarea',
		'TimeZoneEntry'     => 'SwatTimeZoneEntry',
		'ToolLink'          => 'SwatToolLink',
		'YesNoFlydown'      => 'SwatYesNoFlydown',
	);

	// }}}
	// {{{ public function display()

	public function display()
	{
		echo '<h3 class="demo-menu-title">Demos:</h3><ul>';

		foreach ($this->entries as $demo => $title) {
			echo '<li><a href="index.php?demo='.$demo.'">'.$title.'</a></li>';
		}

		echo '</ul>';
	}

	// }}}
}

?>
