<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatCheckAll.php');

/**
 * A an extra row containing a "check all" tool.
 */
class SwatTableViewRowCheckAll extends SwatTableViewRow {
	
	public function display(&$columns) {
		echo '<tr>';

		foreach ($columns as $column) {
			$count = 0;

			if ($column->name == 'checkbox') {
				$td_tag = new SwatHtmlTag('td');
				$td_tag->colspan = count($columns) - $count;

				$check_all = new SwatCheckAll();
				$check_all->series_name = $column->view->name.'_items';

				$td_tag->open();
				$check_all->display();
				$td_tag->close();

				break;

			} else {
				$count++;
				echo '<td>&nbsp;</td>';
			}
		}

		echo '</tr>';
	}

}
?>
