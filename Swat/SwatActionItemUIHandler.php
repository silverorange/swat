<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatUIHandler.php');

/**
 * UI handler for SwatActions.
 */
class SwatActionItemUIHandler extends SwatObject implements SwatUIHandler {

	/**
	 * Get the name
	 *
	 * Gets the name of the class this handler handles. 
	 */
	public function getName() {
		return 'SwatActionItem';
	}

	/**
	 * Attaches $widget to $parent.
	 * @param SwatWidget $widget SwatWidget to attach
	 * @param SwatActionItem $parent Parent action
	 */
	public function attachToParent($widget, $parent) {

		if ($parent->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an SwatActionItem');

		$parent->widget = $widget;
	}
}

?>
