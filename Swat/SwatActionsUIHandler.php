<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatUIHandler.php');
require_once('Swat/SwatActions.php');
require_once('Swat/SwatActionItem.php');

/**
 * UI handler for SwatActions.
 */
class SwatActionsUIHandler implements SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName() {
		return 'SwatActions';
	}

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent) {

		if ($widget instanceof SwatActionItem)
			$parent->addActionItem($widget);
		else
			throw new SwatException('SwatUI: Only '.
				'SwatActionItems can be nested within SwatActions');
	}
}

?>
