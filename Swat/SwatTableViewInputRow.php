<?php

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatWidgetNotFoundException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A table-view row that allows the user to enter data
 *
 * TODO: work out ids. id is required
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewInputRow extends SwatTableViewRow
{
	/**
	 * The text to display in the link to enter a new row
	 *
	 * @var string
	 */
	public $enter_text = '';

	/**
	 * The number of rows to display
	 *
	 * This row can display an arbitrary number of copies of itself. This value
	 * specifies how many copies to display by default.
	 *
	 * @var integer
	 */
	public $number = 1;

	/**
	 * 
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 *
	 * @var string
	 */
	private $row_string;

	/**
	 * Creates a new input row
	 */
	public function __construct()
	{
		$this->enter_text = Swat::_('enter another');
		$this->addJavaScript('swat/javascript/swat-table-view-input-row.js');
	}

	public function init()
	{
		parent::init();
	
		$replicated_widgets = array();
		
	}

	public function process()
	{
		parent::process();

		// retrieve the number of rows
		$this->number =
			$this->view->getForm()->getHiddenField($this->id.'_number');

		// process columns
		$columns = $this->view->getColumns();
		foreach ($columns as $column)
			if ($column->hasInputCell($this->id))
				for ($i = 0; $i < $this->number; $i++)
					$column->getInputCell($this->id)->process($i);
	}

	/**
	 * Sets the control for a specific column in this row's table-view
	 *
	 * Throws an exception if the table-view of the column is not the same
	 * as the table-view of this row.
	 *
	 * @param SwatTableViewColumn $column the column to set the widget for.
	 * @param SwatWidget $widget the widget to add to the specified column in
	 *                            this row's table-view.
	 *
	 * @throws SwatException
	 */
	public function setWidgetForColumn(SwatTableViewColumn $column,
		SwatWidget $widget)
	{
		// TODO: throw more specific exception
		if ($this->view !== $column->view)
			throw new SwatException('Cannot set the widget of a column not '.
				"in this row's table.");

		$cell = new SwatInputCell();
		$cell->row = $this->id;
		$cell->setWidget($widget);
		$column->addInputCell($cell);
	}
	
	/**
	 * Displays this row
	 *
	 * Displays however many copies of this row the user specified and
	 * displays the JavaScript required to enter new rows.
	 *
	 * @param array a reference to the array of {@link SwatTableViewColumn}
	 *               objects in this row's table-view.
	 */
	public function display(&$columns)
	{
		// add number of fields to the form as a hidden field
		$this->view->getForm()->addHiddenField($this->id.'_number',
			$this->number);

		$this->displayInputRows($columns);
		$this->displayEnterAnotherRow($columns);

		$this->row_string = $this->getRowString($columns);
	}

	private function displayInputRows(&$columns)
	{
		// display data input columns
		for ($i = 0; $i < $this->number; $i++) {

			$tr_tag = new SwatHtmlTag('tr');
			$tr_tag->open();

			foreach ($columns as $column) {
				$td_attributes =
					$column->getRendererByPosition()->getTdAttributes();

				$td_tag = new SwatHtmlTag('td', $td_attributes);
				$td_tag->open();

				if ($column->hasInputCell($this->id))
					$column->getInputCell($this->id)->display($i);
				else
					echo '&nbsp;';

				$td_tag->close();
			}
			$tr_tag->close();
		}
	}

	private function displayEnterAnotherRow(&$columns)
	{
		/*
		 * Get column position of enter-a-new-row text. The text is displayed
		 * underneath the first input cell that is not blank.
		 */
		$start_position = 0;
		foreach ($columns as $column) {
			if ($column->hasInputCell($this->id))
				break;

			$start_position++;
		}
		$close_length = count($columns) - $start_position - 1;

		// display the enter-a-new-row row
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id.'_enter_row';
		$tr_tag->open();


		if ($start_position > 0) {
			$td = new SwatHtmlTag('td');
			$td->colspan = $start_position;
			$td->open();
			echo '&nbsp;';
			$td->close();
		}

		// use the same style as table-view column
		$td = new SwatHtmlTag('td',
			$column->getRendererByPosition()->getTdAttributes());

		$td->open();

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->setContent($this->enter_text);
		$anchor_tag->href = "javascript:{$this->id}_obj.addRow();";
		$anchor_tag->class = 'swat-table-view-input-row-enter';
		$anchor_tag->display();

		$td->close();

		if ($close_length > 0) {
			$td = new SwatHtmlTag('td');
			$td->colspan = $close_length;
			$td->open();
			echo '&nbsp;';
			$td->close();
		}

		$tr_tag->close();
	}

	/**
	 * Gets this input row as an XHTML table row with the row identifier as a
	 * placeholder '%s'
	 *
	 * Returning the row identifier as a placeholder means we can use this
	 * function to display multiple copies of this row just by substituting
	 * a new identifier.
	 *
	 * @param array a reference to the array of {@link SwatTableViewColumn}
	 *               objects in this row's table-view.
	 *
	 * @return string this input row as an XHTML table row with the row
	 *                 identifier as a placeholder '%s'.
	 */
	private function getRowString(&$columns)
	{
		ob_start();

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		foreach ($columns as $column) {
			$td_attributes =
				$column->getRendererByPosition()->getTdAttributes();

			$td_tag = new SwatHtmlTag('td', $td_attributes);
			$td_tag->open();

			$suffix = '_'.$this->id.'_%s';

			if ($column->hasInputCell($this->id)) {
				$widget = $column->getInputCell($this->id)->getWidget();
				if ($widget->id !== null)
					$widget->id.= $suffix;

				if ($widget instanceof SwatContainer) {
					$descendants = $widget->getDescendants();
					foreach ($descendants as $descendant)
						if ($descendant->id !== null)
							$descendant->id.= $suffix;
				}
				$widget->display();
			} else {
				echo '&nbsp;';
			}

			$td_tag->close();
		}
		$tr_tag->close();

		return ob_get_clean();
	}

	/**
	 * Creates a JavaScript object to control the client behaviour of this
	 * input row
	 *
	 * @return string
	 */
	public function getInlineJavaScript()
	{
		/*
		 * Encode row string
		 *
		 * Mimize entities so that we do not have to specify a DTD when parsing
		 * the final XML string. If we specify a DTD, Internet Explorer takes a
		 * long time to strictly parse everything. If we do not specify a DTD
		 * and try to parse the final XML string with XHTML entities in it we
		 * get an undefined entity error.
		 */
		$row_string = SwatString::minimizeEntities($this->row_string);
		$row_string = str_replace("'", "&apos;", $row_string);

		// encode newlines for JavaScript string
		$row_string = str_replace("\n", '\n', $row_string);

		return sprintf("var %s_obj = new SwatTableViewInputRow('%s', '%s');",
			$this->id, $this->id, trim($row_string));
	}
}

?>
