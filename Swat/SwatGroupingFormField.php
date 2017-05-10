<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * A grouping form field
 *
 * A specialized form field that semantically groups controls in an
 * XHTML 'fieldset' tag.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatGroupingFormField extends SwatFormField
{
	// {{{ protected function getTitleTag()

	/**
	 * Get a SwatHtmlTag to display the title.
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @return SwatHtmlTag a tag object containing the title.
	 */
	protected function getTitleTag()
	{
		$legend_tag = new SwatHtmlTag('legend');
		$legend_tag->setContent($this->title, $this->title_content_type);

		return $legend_tag;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this form field
	 *
	 * Associates a label with the first widget of this container.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->getFirst() === null)
			return;

		SwatWidget::display();

		$container_tag = new SwatHtmlTag('div');
		$container_tag->id = $this->id;
		$container_tag->class = $this->getCSSClassString();

		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->class = 'swat-grouping-form-field-fieldset';

		$container_tag->open();
		$fieldset_tag->open();
		$this->displayTitle();
		$this->displayContent();
		$this->displayNotes();
		$fieldset_tag->close();
		$this->displayMessages();
		$container_tag->close();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this footer form field
	 *
	 * @return array the array of CSS classes that are applied to this footer
	 *                form field.
	 */
	protected function getCSSClassNames()
	{
		$classes = parent::getCSSClassNames();
		array_unshift($classes, 'swat-grouping-form-field');
		return $classes;
	}

	// }}}
}

?>
