<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A visible grouping of rows in a SwatTableView.
 */
class SwatTableViewGroup extends SwatTableViewColumn {

	/**
	 * @var string The field of the table store to group rows by.
	 */
	public $group_by = null;

	private $current = null;

	protected function displayRenderers($row) {

		if ($this->group_by == null)
			throw new SwatException(__CLASS__.': group_by attribute not set');

		$group_by = $this->group_by;

		if ($row->$group_by == $this->current)
			return;

		$this->current = $row->$group_by;

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		reset($this->renderers);
		$first_renderer = current($this->renderers);
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttribs());
		$td_tag->colspan = $this->view->getColumnCount();
		$td_tag->class = 'swat-table-view-group';
		$td_tag->open();

		$prefix = ($this->view == null)? '': $this->view->name.'_';

		foreach ($this->renderers as $renderer) {
			$renderer->render($prefix);
			echo ' ';
		}

		$td_tag->close();
		$tr_tag->close();
	}
}
