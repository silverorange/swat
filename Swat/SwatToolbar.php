<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A toolbar container for a group of related {@link SwatToolLink} objects
 *
 * @package   Swat
 * @copyright 2005-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolbar extends SwatDisplayableContainer
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
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this toolbar as an unordered list with each sub-item
	 * as a list item
	 */
	public function display(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		SwatWidget::display($context);

		$toolbar_ul = new SwatHtmlTag('ul');
		$toolbar_ul->id = $this->id;
		$toolbar_ul->class = $this->getCSSClassString();

		$toolbar_ul->open($context);
		$this->displayChildren($context);
		$toolbar_ul->close($context);

		$context->addStyleSheet('packages/swat/styles/swat-toolbar.css');
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
		foreach ($this->getToolLinks() as $tool)
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
		$tools = array();
		foreach ($this->getDescendants('SwatToolLink') as $tool)
			if ($tool->getFirstAncestor('SwatToolbar') === $this)
				$tools[] = $tool;

		return $tools;
	}

	// }}}
	// {{{ protected function displayChildren()

	/**
	 * Displays the child widgets of this container
	 */
	protected function displayChildren(SwatDisplayContext $context)
	{
		foreach ($this->children as &$child) {
			ob_start();
			$child->display($context);
			$content = ob_get_clean();
			if ($content != '') {
				$context->out('<li>'.$content.'</li>');
			}
		}
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this tool bar
	 *
	 * @return array the array of CSS classes that are applied to this tool bar.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-toolbar');

		if ($this->parent instanceof SwatContainer) {
			$children = $this->parent->getChildren();
			if (end($children) === $this) {
				$classes[] = 'swat-toolbar-end';
			}
		}

		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
