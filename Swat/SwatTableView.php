<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');

/**
 * A widget to display data in a tabular form.
 */
class SwatTableView extends SwatControl {
	
	/**
	 * A SwatTableModel to display, or null.
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * Whether to show a check all box at the bottom.
	 * @var boolean
	 */
	public $show_check_all = false;

	private $columns;

	public function init() {
		$this->columns = array();
	}

	public function appendColumn(SwatTableViewColumn $column) {
		$this->columns[] = $column;
	}

	public function display() {
		if ($this->model == null) return;

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-table-view';

		$table_tag->open();
		$this->displayHeader();
		$this->displayContent();

		if ($this->show_check_all)
			$this->displayCheckAll();

		$table_tag->close();
	}

	private function displayHeader() {
		echo '<tr>';

		foreach ($this->columns as $column)
			echo '<th>', $column->title, '</th>';

		echo '</tr>';
	}

	private function displayContent() {
		$count = 0;

		foreach ($this->model->getRows() as $id => $row) {

			$count++;
			echo ($count % 2 == 1) ? '<tr class="odd">' : '<tr>';

			foreach ($this->columns as $column)
				$column->display($row);

			echo '</tr>';
		}
	}

	private function displayCheckAll() {
		echo '<tr>';

		foreach ($this->columns as $column) {
			$count = 0;

			if ($column->name == 'checkbox') {
				$td_tag = new SwatHtmlTag('td');
				$td_tag->colspan = count($this->columns) - $count;

				$input_tag = new SwatHtmlTag('input');
				$input_tag->type = 'checkbox';
				$input_tag->name = 'check_all';

				$label_tag = new SwatHtmlTag('label');
				$label_tag->for = 'check_all';

				$td_tag->open();
				$label_tag->open();
				$input_tag->display();
				echo _S('Check All');
				$label_tag->close();
				$td_tag->close();
			} else {
				$count++;
				echo '<td>&nbsp;</td>';
			}
		}

		echo '</tr>';
	}

}
?>
