<?php

/**
 * Abstract base class for displaying SwatError objects
 *
 * A custom error displayer can be used to change how uncaught errors are
 * displayed in an application. For example, you may want to display errors
 * in a separate file or display them using different XHTML markup.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatError::setDisplayer()
 */
abstract class SwatErrorDisplayer
{
	// {{{ public abstract function display()

	/**
	 * Displays a SwatError
	 *
	 * This is called by SwatError::process().
	 */
	public abstract function display(SwatError $e);

	// }}}
}

?>
