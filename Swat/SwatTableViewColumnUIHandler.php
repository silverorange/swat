<?php
require_once('Swat/SwatCellRenderer.php');

/**
 * UI handler for {@link SwatTableViewColumn}
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableViewColumnUIHandler extends SwatObject implements SwatUIHandler {

	/**
	 * Get the name of the class this handler handles
	 */
	public function getName() {
		return 'SwatTableViewColumn';
	}

	/**
	 * Attaches $widget to $parent
	 *
	 * @param SwatWidget $widget
	 * @param ??? TODO: find out what this is
	 */
	public function attachToParent($widget, $parent) {

		if ($widget instanceof SwatCellRenderer)
			$parent->addRenderer($widget);
		else
			throw new SwatException('SwatUI: Only '.
				'SwatCellRenders can be nested within SwatTableViewsColumns');

	}
}

?>
