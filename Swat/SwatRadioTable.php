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
	public function display()
	{
		$options = $this->getOptions();

		if (!$this->visible || $options === null)
			return;
		
		// Empty string XHTML option value is assumed to be null
		// when processing.
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatOption('', $this->blank_title)),
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
}

?>
