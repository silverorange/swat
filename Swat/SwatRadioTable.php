<?php

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

		$this->addStyleSheet('packages/swat/styles/swat-radio-table.css');
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || $options === null)
			return;
		
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatOption(null, $this->blank_title)),
				$options);

		$table_tag = new SwatHtmlTag('table');
		$table_tag->id = $this->id.'_table';
		$table_tag->class = 'swat-radio-table';
		
		$table_tag->open();

		foreach ($options as $option) {	
			echo '<tr>';

			if ($option instanceof SwatFlydownDivider) {
				//ignore these for now TODO: make dividers work with radiolists
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
}

?>
