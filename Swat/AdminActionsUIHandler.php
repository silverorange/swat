<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatUIHandler.php');
require_once('Admin/AdminActions.php');
require_once('Admin/AdminActionItem.php');

/**
 * UI handler for AdminActions.
 */
class AdminActionsUIHandler implements SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName() {
		return 'AdminActions';
	}

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent) {

		if ($widget instanceof AdminActionItem)
			$parent->addActionItem($widget);
		else
			throw new SwatException('SwatUI: Only '.
				'AdminActionItems can be nested within AdminActions');
	}
}

?>
