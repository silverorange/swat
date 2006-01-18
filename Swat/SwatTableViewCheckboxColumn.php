<?php

require_once('Swat/SwatCheckboxCellRenderer.php');
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewCheckAllRow.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A checkbox column.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewCheckboxColumn extends SwatTableViewColumn
{
	/**
	 * Show check all
	 *
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column with an id of "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * Highlight row
	 *
	 * Whether to add JavaScript to highlight the current row of a
	 * {@link SwatTableView} when the checkbox is clicked.
	 *
	 * @var boolean
	 */
	public $highlight_row = true;

	private $items = null;

	private $checkbox_renderer = null;

	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->addJavaScript(
			'swat/javascript/swat-table-view-checkbox-column.js');
	}

	public function init()
	{
		parent::init();
		if ($this->show_check_all)
			$this->view->appendRow(new SwatTableViewCheckAllRow($this->id));
	}

	public function process()
	{
		$item_name = $this->getRendererName();

		if (isset($_POST[$item_name]) && is_array($_POST[$item_name]))
			$this->items = $_POST[$item_name];
	}

	public function getItems()
	{
		return $this->items;
	}

	private function getRendererName()
	{
		$renderer = $this->getCheckboxRenderer();

		return $renderer->id;
	}

	private function getCheckboxRenderer()
	{
		foreach ($this->renderers as $renderer) 
			if ($renderer instanceof SwatCheckboxCellRenderer)
				return $renderer;

		throw new SwatException("The column '{$this->id}' must contain a ".
			'checkbox cell renderer.');
	}

	/**
	 * Gets the inline JavaScript for the highlight row
	 *
	 * @return string the inline JavaScript for the highlight row.
	 *
	 * @see SwatTableViewColumn::getInlineJavaScript()
	 */
	public function getInlineJavaScript()
	{
		if (!$this->highlight_row)
			return '';

		$item_name = $this->getRendererName();
		return "var {$this->id} = new SwatTableViewCheckboxColumn(".
			"'{$item_name}', {$this->view->id});";
	}
}

?>
