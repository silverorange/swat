<?php
require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatCheckAll.php');

/**
 * A an extra row containing a "check all" tool
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableViewCheckAllRow extends SwatTableViewRow {
	
	private $column_name;

	public function __construct($column_name) {
		$this->column_name = $column_name;
	}

	public function display(&$columns) {

		if ($this->view->model->getRowCount() < 2)
			return;		

		echo '<tr>';

		// find column
		$count = 0;
		foreach ($columns as $column) {
			if ($column->name == $this->column_name) {

				$check_all = new SwatCheckAll();
				$check_all->series_name = $column->view->name.'_items';

				break;

			} else {
				$count++;
			}
		}

		if ($count) {
			
			$td_before_tag = new SwatHtmlTag('td');
			if ($count > 1)
				$td_before_tag->colspan = $count;
			
			$td_before_tag->open();
			echo '&nbsp;';
			$td_before_tag->close();

		}
		
		$td_tag = new SwatHtmlTag('td');
		if (count($columns) - $count > 1)
			$td_tag->colspan = count($columns) - $count;

		$td_tag->open();
		$check_all->display();
		$td_tag->close();

		echo '</tr>';
	}

}
?>
