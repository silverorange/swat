<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A toolbar container for a group of related {@link SwatToolLink} objects
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolbar extends SwatContainer
{
	// {{{ public function __construct()

	/**
	 * Creates a new toolbar
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('swat/styles/swat-toolbar.css');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this toolbar as an unordered list with each sub-item
	 * as a list item
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$toolbar_ul = new SwatHtmlTag('ul');
		$toolbar_ul->class = 'swat-toolbar';

		$toolbar_ul->open();

		foreach ($this->children as &$child) {
			echo '<li>';
			$child->display();
			echo '</li>';
		}

		$toolbar_ul->close();
	}

	// }}}
	// {{{ public function setToolLinkValues()

	/**
	 * Sets the value of all {@link SwatToolLink} objects within this toolbar
	 *
	 * This is usually more convenient than setting all the values by hand
	 * if the values are dynamic.
	 *
	 * @param string $value
	 */
	public function setToolLinkValues($value)
	{
		foreach ($this->getDescendants('SwatToolLink') as $tool)
			$tool->value = $value;
	}

	// }}}
	// {{{ public function getToolLinks()

	/**
	 * Gets the tool links of this toolbar
	 * 
	 * Returns an the array of {@link SwatToolLink} objects contained
	 * by this toolbar.
	 *
	 * @return array the tool links contained by this toolbar.
	 */
	public function getToolLinks()
	{
		return $this->getDescendants('SwatToolLink');
	}

	// }}}
}

?>
