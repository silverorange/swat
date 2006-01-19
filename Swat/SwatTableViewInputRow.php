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
 * This row object allows the user to enter data in a manner similar to how the
 * data is displayed. This makes data entry easier as the user can see examples
 * of the type of data they are entering above the fields in which they enter
 * data.
 *
 * Additionally, this row object makes data entry faster by allowing the user
 * to enter an arbitrary number of rows of data at the same time.
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
	 * Defaults to 'enter another'.
	 *
	 * @var string
	 */
	public $enter_text = '';

	/**
	 * The number of rows to display
	 *
	 * This row can display an arbitrary number of copies of itself. This value
	 * specifies how many copies to display by default. This number is set to
	 * the number of entered rows in {@link SwatTableViewInputRow::process()}.
	 *
	 * @var integer
	 */
	public $number = 1;

	/**
	 * A unique identifier for this row
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * An array of input cells for this row indexed by column id
	 *
	 * The array is of the form:
	 * <code>
	 * array('column_id' => $input_cell);
	 * </code>
	 *
	 * @var array
	 */
	private $input_cells = array();

	/**
	 * Creates a new input row
	 */
	public function __construct()
	{
		$this->enter_text = Swat::_('enter another');
		$this->addJavaScript('swat/javascript/swat-table-view-input-row.js');
	}

	/**
	 * Initializes this input row
	 *
	 * This initializes each input cell in this row.
	 *
	 * @see SwatTableViewRow::init()
	 */
	public function init()
	{
		parent::init();

		// init input cells
		foreach ($this->input_cells as $cell)
			$cell->init();
	}

	/**
	 * Processes this input row
	 *
	 * This gets the number of rows the user entered as well as processing
	 * all cloned widgets in input cells that the user submitted.
	 */
	public function process()
	{
		parent::process();

		// retrieve the number of rows
		$this->number =
			$this->view->getForm()->getHiddenField($this->id.'_number');

		// process columns
		$columns = $this->view->getColumns();
		foreach ($columns as $column)
			if (isset($this->input_cells[$column->id]))
				for ($i = 0; $i < $this->number; $i++)
					$this->input_cells[$column->id]->process($i);
	}

	/**
	 * Adds an input cell to this row from a column
	 *
	 * This method is called in {@link SwatTableViewColumn::init()} to move
	 * input cells from the column to this row object. Attaching input cells
	 * directly to their row makes row initilization and processing easier.
	 *
	 * This method may also be called manually to add an input cell directly
	 * to an input row based on a table-view column.
	 *
	 * @param SwatInputCell $cell the input cell to add to this row.
	 * @param string $column_id the unique identifier of the table column. If
	 *                           an id is chosen that does not exist in this
	 *                           row's table-view, and exception is thrown.
	 *
	 * @throws SwatException
	 */
	public function addInputCell(SwatInputCell $cell, $column_id)
	{
		if (!$this->parent->hasColumn($column_id))
			throw new SwatException('Cannot add input cell given a '.
				'non-existant column identifier. Make sure the column you are '.
				'identifying has an identifier set.');

		$this->input_cells[$column_id] = $cell;
	}

	/**
	 * Displays this row
	 *
	 * Uses widget cloning inside {@link SwatInputCell} to display the number
	 * or rows specified and also displays the 'enter-another-row' button.
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

	/**
	 * Displays the actual XHTML input rows for this input row
	 *
	 * Displays the number of rows specified in the property
	 * {@link SwatTableViewInputRow::$number}. Each row is displayed using
	 * cloned widgets inside {@link SwatInputCell} objects.
	 *
	 * @param array a reference to the array of {@link SwatTableViewColumn}
	 *               objects in this row's table-view.
	 */
	private function displayInputRows(&$columns)
	{
		for ($i = 0; $i < $this->number; $i++) {

			$tr_tag = new SwatHtmlTag('tr');
			$tr_tag->open();

			foreach ($columns as $column) {
				// use the same style as table-view column
				$td_attributes =
					$column->getRendererByPosition()->getTdAttributes();

				$td_tag = new SwatHtmlTag('td', $td_attributes);
				$td_tag->open();

				if (isset($this->input_cells[$column->id]))
					$this->input_cells[$column->id]->display($i);
				else
					echo '&nbsp;';

				$td_tag->close();
			}
			$tr_tag->close();
		}
	}

	/**
	 * Displays the enter-another-row row
	 *
	 * @param array a reference to the array of {@link SwatTableViewColumn}
	 *               objects in this row's table-view.
	 */
	private function displayEnterAnotherRow(&$columns)
	{
		/*
		 * Get column position of enter-a-new-row text. The text is displayed
		 * underneath the first input cell that is not blank.
		 */
		$start_position = 0;
		foreach ($columns as $column) {
			if (isset($this->input_cells[$column->id]))
				break;

			$start_position++;
		}
		$close_length = count($columns) - $start_position - 1;

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

			if (isset($this->input_cells[$column->id])) {
				$widget = $this->input_cells[$column->id]->getWidget();
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
		$row_string = $this->getRowString();
		$row_string = SwatString::minimizeEntities($row_string);
		$row_string = str_replace("'", "&apos;", $row_string);

		// encode newlines for JavaScript string
		$row_string = str_replace("\n", '\n', $row_string);

		return sprintf("var %s_obj = new SwatTableViewInputRow('%s', '%s');",
			$this->id, $this->id, trim($row_string));
	}
}

?>
