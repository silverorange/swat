<?php

/**
 * Interface for UI handlers
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
interface SwatUIHandler {

	/**
	 * Gets the name of the class this handler handles.
	 */
	public function getName();

	/**
	 * Attaches $widget to $parent.
	 */
	public function attachToParent($widget, $parent);
}

?>
