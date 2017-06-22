<?php

/**
 * An phone number entry widget
 *
 * @package   Swat
 * @copyright 2010-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPhoneEntry extends SwatEntry
{
	// {{{ protected function getInputTag()

	/**
	 * Get the input tag to display
	 *
	 * @return SwatHtmlTag the input tag to display.
	 */
	protected function getInputTag()
	{
		$tag = parent::getInputTag();
		$tag->type = 'tel';
		return $tag;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                entry.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-phone-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
