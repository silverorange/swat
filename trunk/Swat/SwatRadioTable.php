<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatRadioList.php';

/**
 * Special radio-list that can display multi-line list items using a
 * tabular format
 *
 * @package   Swat
 * @copyright 2006 silverorange
 */
class SwatRadioTable extends SwatRadioList
{
	// {{{ public function __construct()

	/**
	 * Creates a new radio table
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('packages/swat/styles/swat-radio-table.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || $options === null)
			return;

		// add a hidden field so we can check if this list was submitted on
		// the process step
		$this->getForm()->addHiddenField($this->id.'_submitted', 1);
		
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatOption(null, $this->blank_title)),
				$options);

		$table_tag = new SwatHtmlTag('table');
		$table_tag->id = $this->id;
		$table_tag->class = $this->getCSSClassString();
		$table_tag->open();

		foreach ($options as $option) {	
			echo '<tr>';

			if ($option instanceof SwatFlydownDivider) {
				echo '<td class="swat-radio-table-input">';
				echo '&nbsp;';
				echo '</td><td>';
				$this->displayDivider($option);
				echo '</td>';
			} else {					
				echo '<td class="swat-radio-table-input">';
				$this->displayOption($option);
				echo '</td><td>';
				$this->displayOptionLabel($option);
				echo '</td>';
			}

			echo '</tr>';
		}

		$table_tag->close();
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
