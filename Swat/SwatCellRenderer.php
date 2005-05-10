<?php

require_once('Swat/SwatObject.php');

/**
 * Object for rendering a single cell
 *
 * Subclasses add public class variable to store data they need for rendering.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
abstract class SwatCellRenderer extends SwatObject {

	/**
	 * The widget which contains this cell renderer
	 * @var SwatWidget
	 */
	public $parent = null;

	/**
	 * Render the cell
	 *
	 * Render the cell using the values currently stored in class variables.
	 * Cell renderer subclasses should implement this method to do all
	 * output neccessary to display the cell.
	 *
	 * @param string $prefix Optional prefix to name HTML controls with.
	 */
	abstract public function render($prefix);

	/**
	 * Get TD-tag Attributes
	 *
	 * Array of attributes to assign to the HTML TD tag.
	 * Sub-classes can redefine this to set attributes on the TD tag.
	 */
	public function getTdAttribs() {
		return null;
	}

}

?>
