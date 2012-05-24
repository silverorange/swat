<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatRadioList.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Special radio-list that can display multi-line list items using a
 * tabular format
 *
 * @package   Swat
 * @copyright 2006-2012 silverorange
 */
class SwatRadioTable extends SwatRadioList
{
	// {{{ public function display()

	public function display(SwatDisplayContext $context)
	{
		$options = $this->getOptions();

		if (!$this->visible || $options === null) {
			return;
		}

		SwatWidget::display($context);

		// add a hidden field so we can check if this list was submitted on
		// the process step
		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		if ($this->show_blank) {
			$options = array_merge(
				array(new SwatOption(null, $this->blank_title)),
				$options
			);
		}

		$table_tag = new SwatHtmlTag('table');
		$table_tag->id = $this->id;
		$table_tag->class = $this->getCSSClassString();
		$table_tag->open($context);

		foreach ($options as $index => $option) {
			$this->displayRadioTableOption($context, $option, $index);
		}

		$table_tag->close($context);

		$context->addStyleSheet('packages/swat/styles/swat-radio-table.css');
	}

	// }}}
	// {{{ protected function displayRadioTableOption()

	/**
	 * Displays a single option in this radio table
	 *
	 * @param SwatOption $option the option to display.
	 * @param integer $index the numeric index of the option in this list.
	 */
	protected function displayRadioTableOption(SwatDisplayContext $context,
		SwatOption $option, $index)
	{
		$tr_tag = $this->getTrTag($option, $index);

		// add option-specific CSS classes from option metadata
		$classes = $this->getOptionMetadata($option, 'classes');
		if (is_array($classes)) {
			$tr_tag->class = implode(' ', $classes);
		} elseif ($classes) {
			$tr_tag->class = strval($classes);
		}

		$tr_tag->open($context);

		if ($option instanceof SwatFlydownDivider) {
			$context->out('<td class="swat-radio-table-input">');
			$context->out('&nbsp;');
			$context->out('</td><td class="swat-radio-table-label">');
			$this->displayDivider($context, $option);
			$context->out('</td>');
		} else {
			$context->out('<td class="swat-radio-table-input">');
			$this->displayOption($context,$option);
			$context->out(
				sprintf(
					'</td><td id="%s" class="swat-radio-table-label">',
					$this->id.'_'.(string)$option->value.'_label'
				)
			);

			$this->displayOptionLabel($context, $option);
			$context->out('</td>');
		}

		$tr_tag->close($context);
	}

	// }}}
	// {{{ protected function getTrTag()

	/**
	 * Gets the tr tag used to display a single option in this radio table
	 *
	 * @param SwatOption $option the option to display.
	 * @param integer $index the numeric index of the option in this list.
	 */
	protected function getTrTag(SwatOption $option, $index)
	{
		return new SwatHtmlTag('tr');
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this radio table
	 *
	 * @return array the array of CSS classes that are applied to this radio
	 *                table.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-radio-table');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
