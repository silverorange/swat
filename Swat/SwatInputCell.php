<?php

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatWidget.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * A cell container that contains a widget and is bound to a
 * {@link SwatTableViewInputRow} object
 *
 * Input cells are placed inside table-view columns and are used by input-rows
 * to display and process user data entry rows.
 *
 * This input cell object is required to bind a widget, a row and a column
 * together.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInputCell extends SwatUIObject implements SwatUIParent, SwatTitleable
{
	// {{{ private properties

	/**
	 * A lookup array for widgets contained in this cell
	 *
	 * The array is multidimentional and is of the form:
	 * <code>
	 * array(
	 *     0 => array('widget_id' => $widget0_reference),
	 *     1 => array('widget_id' => $widget1_reference)
	 * );
	 * </code>
	 * The 0 and 1 represent numeric row identifiers. The 'widge_id' string
	 * represents the original identifier of the widget in this cell. The
	 * widget references are references to the cloned widgets.
	 *
	 * @var array
	 */
	private $widgets = array();
	
	/**
	 * A cache of cloned widgets
	 *
	 * This cache is used so we only have to clone widgets once per page load.
	 * The array is of the form:
	 * <code>
	 * array(
	 *     0 => $widget0_reference,
	 *     1 => $widget1_reference
	 * );
	 * </code>
	 * where 0 and 1 are numeric row identifiers.
	 *
	 * @var array
	 */
	private $clones = array();

	// }}}
	// {{{ protected properties

	/**
	 * The widget displayed in this cell
	 *
	 * @var SwatWidget
	 */
	protected $widget = null;

	// }}}
	// {{{ public function addChild()
	
	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To set the widget for an input cell use
	 * {@link SwatInputCell::setWidget()}.
	 *
	 * If you try to add more than one widget to this cell, an exception is
	 * thrown.
	 *
	 * @param SwatWidget $child a reference to a child object to add.
	 *
	 * @see SwatUIParent, SwatUI, SwatInputCell::setWidget()
	 *
	 * @throws SwatException
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->widget === null)
			$this->setWidget($child);
		else
			throw new SwatException('Can only add one widget to an input '.
				'cell. Use a container if you want to add multiple widgets.');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this input cell
	 *
	 * This calls {@link SwatWidget::init()} on the cell's widget.
	 */
	public function init()
	{
		if ($this->widget !== null)
			$this->widget->init();

		// ensure the widget has an id
		if ($this->widget->id === null)
			$this->widget->id = $this->widget->getUniqueId();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this input cell given a numeric row identifier
	 *
	 * This creates a cloned widget for the given numeric identifier and then
	 * processes the widget.
	 *
	 * @param integer $row_identifier the numeric identifier of the input row
	 *                                 the user entered.
	 */
	public function process($row_identifier)
	{
		$widget = $this->getClonedWidget($row_identifier);
		$widget->process();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this input cell given a numeric row identifier
	 *
	 * This creates a cloned widget for the given numeric identifier and then
	 * displays the widget.
	 *
	 * @param integer $row_indentifier the numeric identifier of the input row
	 *                                  that is being displayed.
	 */
	public function display($row_identifier)
	{
		$widget = $this->getClonedWidget($row_identifier);
		$widget->display();
	}

	// }}}
	// {{{ public function getTitle()

	/**
	 * Gets the title of this input cell
	 *
	 * Implements the {SwatTitleable::getTitle()} interface.
	 *
	 * @return the title of this input cell.
	 */
	public function getTitle()
	{
		if ($this->parent === null)
			return '';
		else
			return $this->parent->title;
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 * Sets the widget of this input cell
	 *
	 * @param SwatWidget $widget the new widget of this input cell.
	 */
	public function setWidget(SwatWidget $widget)
	{
		$this->widget = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ public function getPrototypeWidget()

	/** 
	 * Gets the widget of this input cell
	 *
	 * You usually want to get one of the cloned widgets in this cell. This can
	 * be done easiest through the {@link SwatTableViewInputRow::getWidget()}
	 * method.
	 *
	 * @return SwatWidget the widget of this input cell.
	 *
	 * @see SwatTableViewInputRow::getWidget(), SwatInputCell::getWidget()
	 */
	public function getPrototypeWidget()
	{
		return $this->widget;
	}

	// }}}
	// {{{ public function getWidget()

	/**
	 * Gets a particular widget in this input cell
	 *
	 * @param integer $row_identifier the numeric row identifier of the widget.
	 * @param string $widget_id the unique identifier of the widget. If no id
	 *                           is specified, the root widget of this cell is
	 *                           returned for the given row.
	 *
	 * @return SwatWidget the requested widget object.
	 *
	 * @throws SwatException
	 */
	public function getWidget($row_identifier, $widget_id = null)
	{
		$this->getClonedWidget($row_identifier);

		if ($widget_id === null && isset($this->clones[$row_identifier])) {
			return $this->clones[$row_identifier];

		} elseif ($widget_id !== null &&
			isset($this->widgets[$row_identifier][$widget_id])) {

			return $this->widgets[$row_identifier][$widget_id];
		}

		throw new SwatException('The specified widget was not found with the '.
			'specified row identifier.');
	}

	// }}}
	// {{{ public function unsetWidget()

	/**
	 * Unsets a replicated widget within this cell
	 *
	 * This is useful if you are deleting a row from an input row.
	 *
	 * @param integer replicator_id the replicator id of the cloned widget to
	 *                 unset.
	 *
	 * @see SwatTableViewInputRow::removeReplicatedRow()
	 */
	public function unsetWidget($replicator_id)
	{
		if (isset($this->widgets[$replicator_id]))
			unset($this->widgets[$replicator_id]);

		if (isset($this->clones[$replicator_id]))
			unset($this->clones[$replicator_id]);
	}

	// }}}
	// {{{ public function getHtmlHeadEntries()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this row
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this input cell.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = new SwatHtmlHeadEntrySet($this->html_head_entries);
		$out->addEntrySet($this->widget->getHtmlHeadEntries());
		return $out;
	}

	// }}}
	// {{{ protected function getInputRow()

	/**
	 * Gets the input row this cell belongs to
	 *
	 * If this input-cell is not added to a table-view yet or the table-view
	 * this cell is added to does not have an input-row then null is returned.
	 *
	 * @return the input row this cell belongs to.
	 */
	protected function getInputRow()
	{
		$view = $this->getFirstAncestor('SwatTableView');
		if ($view === null)
			return null;

		$row = $view->getFirstRowByClass('SwatTableViewInputRow');

		return $row;
	}

	// }}}
	// {{{ protected function getClonedWidget()

	/**
	 * Gets a cloned widget given a unique identifier
	 *
	 * The cloned widget is stored in the {@link SwatInputCell::$clones} array.
	 *
	 * @param string $replicator_id the unique identifier of the new cloned
	 *                               widget. The actual cloned widget id is
	 *                               constructed from this identifier and from
	 *                               the input row that this input cell belongs
	 *                               to.
	 *                               
	 *
	 * @return SwatWidget the new cloned widget or the cloned widget retrieved
	 *                     from the {@link SwatInputCell::$clones} array.
	 *
	 * @throws SwatException
	 */
	protected function getClonedWidget($replicator_id)
	{
		if (isset($this->clones[$replicator_id]))
			return $this->clones[$replicator_id];

		if ($this->widget === null)
			return null;

		$row = $this->getInputRow();
		if ($row === null)
			throw new SwatException('Cannot clone widgets until cell is '.
				'added to a table-view and an input-row is added to the '.
				'table-view');

		$suffix = '_'.$row->id.'_'.$replicator_id;
		$new_widget = clone $this->widget;

		if ($new_widget->id !== null) {
			$this->widgets[$replicator_id][$new_widget->id] = $new_widget;
			$new_widget->id.= $suffix;
		}

		// TODO: this doesn't work for embedded table views, etc
		if ($new_widget instanceof SwatContainer) {
			$descendants = $new_widget->getDescendants();
			foreach ($descendants as $descendant) {
				if ($descendant->id !== null) {
					$this->widgets[$replicator_id][$descendant->id] =
						$descendant;

					$descendant->id.= $suffix;
				}
			}
		}

		$this->clones[$replicator_id] = $new_widget;

		return $new_widget;
	}

	// }}}
}

?>
