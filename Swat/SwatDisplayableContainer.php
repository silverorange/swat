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
	// {{{ public function display()

	/**
	 * Displays this container
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();

		$div->open();
		$this->displayChildren();
		$div->close();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this displayable
	 * container 
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                displayable container.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-displayable-container');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
