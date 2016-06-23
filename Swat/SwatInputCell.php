<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

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
 * @copyright 2006-2016 silverorange
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
	 * The 0 and 1 represent numeric row identifiers. The 'widget_id' string
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
	 * The prototype widget displayed in this cell
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
	 * called elsewhere. To set the prototype widget for an input cell use
	 * {@link SwatInputCell::setWidget()}.
	 *
	 * @param SwatWidget $child a reference to a child object to add.
	 *
	 * @see SwatUIParent
	 * @see SwatInputCell::setWidget()
	 *
	 * @throws SwatException if you try to add more than one prototype widget
	 *                       to this input cell.
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->widget === null)
			$this->setWidget($child);
		else
			throw new SwatException('Can only add one widget to an input '.
				'cell. Add a SwatContainer instance if you need to add '.
				'multiple widgets.');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this input cell
	 *
	 * This calls {@link SwatWidget::init()} on the cell's prototype widget.
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
	 * @return string the title of this input cell.
	 */
	public function getTitle()
	{
		if ($this->parent === null)
			return '';
		else
			return $this->parent->title;
	}

	// }}}
	// {{{ public function getTitleContentType()

	/**
	 * Gets the title content-type of this input cell
	 *
	 * Implements the {@link SwatTitleable::getTitleContentType()} interface.
	 *
	 * @return string the title content-type of this input cell.
	 */
	public function getTitleContentType()
	{
		return 'text/plain';
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 * Sets the prototype widget of this input cell
	 *
	 * @param SwatWidget $widget the new prototype widget of this input cell.
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
	 * Instead of the prototype widget, you usually want to get one of the
	 * cloned widgets in this cell. This can be done by using the
	 * {@link SwatTableViewInputRow::getWidget()} method or by using the
	 * {@link SwatInpueCell::getWidget()} method.
	 *
	 * @return SwatWidget the prototype widget of this input cell.
	 *
	 * @see SwatTableViewInputRow::getWidget()
	 * @see SwatInputCell::getWidget()
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
	 * @param string $widget_id optional. The unique identifier of the widget.
	 *                           If no <i>$widget_id</i> is specified, the root
	 *                           widget of this cell is returned for the given
	 *                           <i>$row_identifier</i>.
	 *
	 * @return SwatWidget the requested widget object.
	 *
	 * @throws SwatException if the specified widget does not exist in this
	 *                       input cell.
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
	 * Unsets a cloned widget within this cell
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
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this input cell
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this input cell.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->widget->getHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ public function getAvailableHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects that may be needed by this input cell
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
	 *                               needed by this input cell.
	 *
	 * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
	 */
	public function getAvailableHtmlHeadEntrySet()
	{
		$set = parent::getAvailableHtmlHeadEntrySet();
		$set->addEntrySet($this->widget->getAvailableHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * The descendant UI-objects of an input cell are cloned widgets, not the
	 * prototype widget.
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this input cell. If
	 *                descendant objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		foreach ($this->clones as $cloned_widget) {
			if ($class_name === null || $cloned_widget instanceof $class_name) {
				if ($cloned_widget->id === null)
					$out[] = $cloned_widget;
				else
					$out[$cloned_widget->id] = $cloned_widget;
			}

			if ($cloned_widget instanceof SwatUIParent)
				$out = array_merge($out,
					$cloned_widget->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * The descendant UI-objects of an input cell are cloned widgets, not the
	 * prototype widget.
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant widget or null if no matching
	 *                       descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		foreach ($this->clones as $cloned_widget) {
			if ($cloned_widget instanceof $class_name) {
				$out = $cloned_widget;
				break;
			}

			if ($cloned_widget instanceof SwatUIParent) {
				$out = $cloned_widget->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this input cell.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * input cell.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates(array $states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	// }}}
	// {{{ public function copy()

	/**
	 * Performs a deep copy of the UI tree starting with this UI object
	 *
	 * @param string $id_suffix optional. A suffix to append to copied UI
	 *                           objects in the UI tree.
	 *
	 * @return SwatUIObject a deep copy of the UI tree starting with this UI
	 *                       object.
	 *
	 * @see SwatUIObject::copy()
	 */
	public function copy($id_suffix = '')
	{
		$copy = parent::copy($id_suffix);

		if ($this->widget !== null) {
			$copy_widget = $this->widget->copy($id_suffix);
			$copy_widget->parent = $copy;
			$copy->widget = $copy_widget;
		}

		foreach ($this->clones as $replicator_id => $clone) {
			$copy_clone = $clone->copy($id_suffix);
			$copy_clone->parent = $copy;
			$copy->clones[$replicator_id] = $copy_clone;
			if ($copy_child->id !== null) {
				$copy->children_by_id[$copy_child->id] = $copy_child;
			}

			$clone->widgets[$replicator_id] = array();
			$clone->widgets[$replicator_id][$copy_clone->id] = $copy_clone;
			if ($copy_clone instanceof SwatUIParent) {
				foreach ($copy_clone->getDescendants() as $descendant) {
					if ($descendant->id !== null) {
						$copy->widgets[$replicator_id][$descendant->id] =
							$descendant;
					}
				}
			}
		}

		return $copy;
	}

	// }}}
	// {{{ protected function getInputRow()

	/**
	 * Gets the input row this cell belongs to
	 *
	 * If this input-cell is not yet added to a table-view, or if the parent
	 * table-view of this cell does not have an input-row, null is returned.
	 *
	 * @return SwatTableViewInputRow the input row this cell belongs to.
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
	 * @param string $replicator_id the unique identifier of the new cloned
	 *                               widget. The actual cloned widget id is
	 *                               constructed from this identifier and from
	 *                               the input row that this input cell belongs
	 *                               to.
	 *
	 * @return SwatWidget the new cloned widget or the cloned widget retrieved
	 *                     from the {@link SwatInputCell::$clones} array.
	 *
	 * @throws SwatException if this input cell does not belong to a table-view
	 *                       with an input row.
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
				'table-view.');

		$view = $this->getFirstAncestor('SwatTableView');
		$view_id = ($view === null) ? null : $view->id;
		$suffix = ($view_id === null) ? '_'.$row->id.'_'.$replicator_id :
			'_'.$view_id.'_'.$row->id.'_'.$replicator_id;

		$new_widget = $this->widget->copy($suffix);
		$new_widget->parent = $this;

		if ($new_widget->id !== null) {
			// lookup array uses original ids
			$old_id = mb_substr($new_widget->id, 0, -mb_strlen($suffix));
			$this->widgets[$replicator_id][$old_id] = $new_widget;
		}

		if ($new_widget instanceof SwatUIParent) {
			foreach ($new_widget->getDescendants() as $descendant) {
				if ($descendant->id !== null) {
					// lookup array uses original ids
					$old_id = mb_substr(
						$descendant->id,
						0,
						-mb_strlen($suffix)
					);
					$this->widgets[$replicator_id][$old_id] = $descendant;
				}
			}
		}

		$this->clones[$replicator_id] = $new_widget;

		return $new_widget;
	}

	// }}}
}

?>
