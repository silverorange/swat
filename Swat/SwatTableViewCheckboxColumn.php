<?php
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewCheckAllRow.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A checkbox column.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableViewCheckboxColumn extends SwatTableViewColumn {

	/**
	 * Show check all
	 *
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column named "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	public function init() {
		if ($this->name === null)
			$this->name == 'checkbox';

		if ($this->show_check_all)
			$this->view->appendRow(new SwatTableViewCheckAllRow($this->name));
	}

}
