<?php

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatWidgetNotFoundException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A table-view row that allows the user to enter data
 *
 * TODO: getHtmlHeadEntries()
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
/*		
		$replicated_widgets = array();
		$widget = $this->getWidget();
*/
	}

	public function process()
	{
		parent::process();
/*
		$this->number = $_POST[$this->id.'_number'];
		for ($i = 0; $i < $this->number; $i++) {
		}
*/
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
	 * Gets a widget in this row given a column
	 *
	 * If the given column does not have a widget for this row an exception
	 * is thrown.
	 *
	 * @param SwatTableViewColumn $column the column to get the widget from.
	 *
	 * @return SwatWidget the widget from the specified column.
	 *
	 * @throws SwatException
	 */
	public function getWidgetByColumn(SwatTableViewColumn $column)
	{
		// TODO: more specific exception type here
		if (!$column->hasInputCell($this->id))
			throw new SwatException('No widget is attached to the given '.
				'column object.');

		return $column->getInputCell($this->id)->getWidet();
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

		// display data input columns
		$row_string = $this->getString($columns);
		for ($i = 0; $i < $this->number; $i++)
			echo str_replace('%s', (string)$i, $row_string);

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
		$anchor_tag->content = $this->enter_text;
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

		$this->displayJavaScript($row_string);
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
	private function getString(&$columns)
	{
		ob_start();

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();

		foreach ($columns as $column)
		{
			$td_attributes =
				$column->getRendererByPosition()->getTdAttributes();

			$td_tag = new SwatHtmlTag('td', $td_attributes);
			$td_tag->open();

			// TODO: fix ids for sub-widgets. see replicator
			if ($column->hasInputCell($this->id)) {
				$widget = $column->getInputCell($this->id)->getWidget();
				if ($widget->id !== null)
					$widget->id = $this->id.'_%s_'.$widget->id;

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
	 * @param string $row_string
	 */
	private function displayJavaScript($row_string)
	{
		// encode row string
		/*
		 * Mimize entities so that we do not have to specify a DTD when parsing
		 * the final XML string. If we specify a DTD, Internet Explorer takes a
		 * long time to strictly parse everything. If we do not specify a DTD
		 * and try to parse the final XML string we get an undefined entity
		 * error.
		 */
		$row_string = SwatString::minimizeEntities($row_string);
		$row_string = str_replace("'", "&apos;", $row_string);

		// encode newlines for JavaScript string
		$row_string = str_replace("\n", '\n', $row_string);

		echo '<script type="text/javascript">'."\n";
		printf("%s_obj = new SwatTableViewInputRow('%s', '%s');\n",
			$this->id, $this->id, trim($row_string));

		echo '</script>';
	}
}

?>
