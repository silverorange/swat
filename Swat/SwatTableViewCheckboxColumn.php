<?php

require_once('Swat/SwatCheckboxCellRenderer.php');
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
	 * table view must contain a column with an id of "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	private $items = null;

	private $checkbox_renderer = null;

	public function init() {
		// TODO: autogenerate an id here
		if ($this->id === null)
			$this->id == 'checkbox';

		if ($this->show_check_all)
			$this->view->appendRow(new SwatTableViewCheckAllRow($this->id));

			}

	public function process() {
		$renderer = $this->getCheckboxRenderer();
		
		$prefix = ($this->view->id === null)? '': $this->view->id.'_';
		$item_name = $prefix.$renderer->id;
		
		if (isset($_POST[$item_name]) && is_array($_POST[$item_name]))
			$this->items = $_POST[$item_name];
	}

	public function getItems() {
		return $this->items;
	}

	private function getCheckboxRenderer() {
		foreach ($this->renderers as $renderer) 
			if ($renderer instanceof SwatCheckboxCellRenderer)
				return $renderer;
		
		throw new SwatException(__CLASS__
			.": The column '{$this->id}' must have a checkbox renderer");
	}
}

?>
