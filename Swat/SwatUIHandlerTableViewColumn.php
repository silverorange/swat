<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * UI handler for SwatTableViewColumn.
 */
class SwatUIHandlerTableViewColumn implements SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName() {
		return 'SwatTableViewColumn';
	}

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent) {

		if ($widget instanceof SwatCellRenderer)
			$parent->addRenderer($widget);
		else
			throw new SwatException('SwatLayout: Only '.
				'SwatCellRenders can be nested within '.
				'SwatTableViewsColumns ('.$xmlfile.')');

	}
}

?>
