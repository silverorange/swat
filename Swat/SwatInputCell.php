<?php

require_once 'Swat/SwatUIObject.php';
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
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInputCell extends SwatUIObject implements SwatUIParent
{
	// {{{ public properties

	/**
	 * The unique identifier of the input row for this input cell
	 *
	 * @var string
	 */
	public $row = null;

	// }}}
	// {{{ private properties

	/**
	 * The widget displayed in this cell
	 *
	 * @var SwatWidget
	 */
	private $widget = null;

	/**
	 * A cache of cloned widgets
	 *
	 * This cache is used so we only have to clone widgets once per page load.
	 *
	 * @var array
	 */
	private $clones = array();

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
	 * @param integer $row_number the numeric identifier of the input row the
	 *                             user entered.
	 */
	public function process($row_number)
	{
		$widget = $this->getClonedWidget($row_number);
		$widget->process();
		Swat::printObject($widget->id.' :: '.$widget->getState());
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this input cell given a numeric row identifier
	 *
	 * This creates a cloned widget for the given numeric identifier and then
	 * displays the widget.
	 *
	 * @param integer $row_number the numeric identifier of the input row that
	 *                             is being displayed.
	 */
	public function display($row_number)
	{
		$widget = $this->getClonedWidget($row_number);
		$widget->display();
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
	}

	// }}}
	// {{{ public function getWidget()

	/** 
	 * Gets the widget of this input cell
	 *
	 * You usually want to get one of the cloned widgets in this cell. This can
	 * be done easiest through the {@link SwatTableViewInputRow::getWidget()}
	 * method.
	 *
	 * @return SwatWidget the widget of this input cell.
	 *
	 * @see SwatTableViewInputRow::getWidget()
	 */
	public function getWidget()
	{
		return $this->widget;
	}

	// }}}
	// {{{ public function getHtmlHeadEntries()

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this row
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this input cell.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

	// }}}
	// {{{ private function getClonedWidget()

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
	 */
	private function getClonedWidget($replicator_id)
	{
		if (isset($this->clones[$replicator_id]))
			return $this->clones[$replicator_id];

		if ($this->widget === null)
			return null;

		$suffix = '_'.$this->row.'_'.$replicator_id;
		$new_widget = clone $this->widget;

		if ($new_widget->id !== null)
			$new_widget->id.= $suffix;

		// TODO: this doesn't work for embedded table views, etc
		if ($new_widget instanceof SwatContainer) {
			$descendants = $new_widget->getDescendants();
			foreach ($descendants as $descendant)
				if ($descendant->id !== null)
					$descendant->id.= $suffix;
		}

		$this->clones[$replicator_id] = $new_widget;

		return $new_widget;
	}

	// }}}
}

?>
