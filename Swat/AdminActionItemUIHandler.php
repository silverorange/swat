<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatUIHandler.php');

/**
 * UI handler for AdminActions.
 */
class AdminActionItemUIHandler implements SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName() {
		return 'AdminActionItem';
	}

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent) {

		if ($parent->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an AdminActionItem');

		$parent->widget = $widget;
	}
}

?>
