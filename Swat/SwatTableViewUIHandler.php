<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewGroup.php');

/**
 * UI handler for SwatTableView.
 */
class SwatTableViewUiHandler implements SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName() {
		return 'SwatTableView';
	}

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent) {

		if ($widget instanceof SwatTableViewGroup)
			$parent->setGroup($widget);
		elseif ($widget instanceof SwatTableViewColumn)
			$parent->appendColumn($widget);
		else
			throw new SwatException('SwatUI: Only '.
				'SwatTableViewColumns can be nested within SwatTableViews');
	}
}

?>
