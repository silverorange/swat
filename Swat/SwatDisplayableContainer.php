<?php

require_once 'Swat/SwatContainer.php';

/**
 * Abstract base container that displays an XHTML element
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatDisplayableContainer extends SwatContainer
{
	// {{{ public properties

	/**
	 * The custom CSS class of this frame
	 *
	 * This optional class is added on top of the default 'swat-frame'
	 * class.
	 *
	 * @var string
	 */
	public $class = null;

	// }}}
	// {{{ protected function getCssClasses()

	/**
	 * Get CSS classes for outer XHTML element
	 */
	protected function getCssClasses($class)
	{
		if ($this->class !== null)
			$class.= ' '.$this->class;

		return $classes;
	}

	// }}}
}

?>
