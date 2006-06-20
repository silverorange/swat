<?php

require_once 'Swat/SwatContainer.php';

/**
 * Base class for containers that display an XHTML element
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDisplayableContainer extends SwatContainer
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

		return $class;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this container
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div = new SwatHtmlTag('div');
		$div->class = $this->getCssClasses('swat-displayable-container');

		if ($this->id !== null)
			$div->id = $this->id;

		$div->open();
		$this->displayChildren();
		$div->close();
	}

	// }}}
}

?>
