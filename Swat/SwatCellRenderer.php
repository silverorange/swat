<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * An object for rendering a single cell.
 * Subclasses add public class variable to store data they need for rendering.
 */
abstract class SwatCellRenderer extends SwatObject {

	function __construct() {
	
	}

	/*
	 * Render the cell using the values currently stored in class variables.
	 */
	abstract public function render();
}
