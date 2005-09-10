<?php

require_once 'Swat/SwatObject.php';

/**
 * Object for rendering a single cell
 *
 * Subclasses add public class variable to store data they need for rendering.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatCellRenderer extends SwatObject
{
	/**
	 * The table view that contains this cell renderer
	 *
	 * @var SwatTableView
	 */
	public $parent = null;

	/**
	 * A non-visible unique id for this cell renderer, or null
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Renders this cell
	 *
	 * Renders this cell using the values currently stored in class variables.
	 *
	 * Cell renderer subclasses should implement this method to do all
	 * output neccessary to display the cell.
	 */
	abstract public function render();

	/**
	 * Gets TH-tag attributes
	 *
	 * Sub-classes can redefine this to set attributes on the TH tag.
	 *
	 * @return array an array of attributes to apply to the TH tag of the
	 *                column that contains this cell renderer.
	 */
	public function &getThAttributes()
	{
		return array();
	}

	/**
	 * Gets TD-tag attributes
	 *
	 * Sub-classes can redefine this to set attributes on the TD tag.
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 */
	public function &getTdAttributes()
	{
		return array();
	}
}

?>
