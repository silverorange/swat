<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatWidget.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');

/**
 * A widget to display data in a tabular form.
 */
class SwatTableView extends SwatWidget {
	
	/**
	 * A SwatTableModel to display, or null.
	 * @var SwatTableModel
	 */
	public $model = null;

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
		$table_tag->close();
	}

	private function displayHeader() {
		echo '<tr>';

		foreach ($this->columns as $column)
			echo '<th>', $column->title, '</th>';

		echo '</tr>';
		echo "\n";
	}

	private function displayContent() {
		$count = 0;

		foreach ($this->model->getRows() as $id => $row) {

			$count++;
			echo ($count % 2 == 1) ? '<tr class="odd">' : '<tr>';

			foreach ($this->columns as $column)
				$column->display($row);

			echo '</tr>';
			echo "\n";
		}
	}

	public function gatherErrorMessages() {
		return array();
	}
}
?>
