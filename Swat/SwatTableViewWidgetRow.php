<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatWidget.php';

/**
 * A table view row with an optional contained widget
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewWidgetRow extends SwatTableViewRow implements SwatUIParent
{
	// {{{ class constants

	/**
	 * Display the widget in the left cell
	 */
	const POSITION_LEFT = 0;

	/**
	 * Display the widget in the right cell
	 */
	const POSITION_RIGHT = 1;

	// }}}
	// {{{ public properties

	/**
	 * How far from the end of the row the widget should be displayed measured
	 * in columns. The end of the row this offset is relative to is detemined
	 * by the $postion property.
	 *
	 * @var integer
	 */
	public $offset = 0;

	/**
	 * How many table-view columns the widget should span
	 *
	 * @var integer
	 */
	public $span = 1;

	/**
	 * Whether to display the widget in the left or right cell of the row
	 *
	 * By default, the widget displays in the left cell. Use the POSITION_*
	 * constants to control the widget position within this row.
	 *
	 * @var integer
	 */
	public $position = self::POSITION_LEFT;

	// }}}
	// {{{ protected properties

	/**
	 * The contained widget
	 *
	 * @var SwatWidget
	 *
	 * @see SwatTableViewWidgetRow::setWidget()
	 */
	protected $widget;

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To set the widget for this row,
	 * {@link SwatTableViewWidgetRow::setWidget()}.
	 *
	 * @param SwatWidget $child a reference to the child object to add.
	 *
	 * @throws SwatException
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatTableViewWidgetRow::setWidget()
	 */
	public function addChild(SwatObject $child)
	{
		if (!($child instanceof SwatWidget))
			throw new SwatInvalidClassException(sprintf(
				'Only SwatWidget objects may be nested within '.
				'SwatTableViewWidgetRow. Attempting to add "%s".',
				get_class($child)), 0, $child);

		if ($this->widget !== null)
			throw new SwatException(
				'Can only set one widget for a widget row.');

		$this->setWidget($child);
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this widget row. If
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

		if ($this->widget !== null) {
			if ($class_name === null || $this->widget instanceof $class_name) {
				if ($this->widget->id === null)
					$out[] = $this->widget;
				else
					$out[$this->widget->id] = $this->widget;
			}

			if ($this->widget instanceof SwatUIParent)
				$out = array_merge($out,
					$this->widget->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		if ($this->widget instanceof $class_name)
			$out = $this->widget;

		if ($out === null && $this->widget instanceof SwatUIParent)
			$out = $this->widget->getFirstDescendant($class_name);

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this action item.
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
	 * action item.
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
	// {{{ public function setWidget()

	/**
	 * Sets the widget contained in this row
	 *
	 * @param SwatWidget $widget the widget to contain in this row.
	 *
	 * @throws SwatException if the added widget is already the child of
	 *                       another object.
	 */
	public function setWidget(SwatWidget $widget)
	{
		if ($widget->parent !== null)
			throw new SwatException('Attempting to add a widget that already '.
				'has a parent.');

		$this->widget = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ public function getWidget()

	/**
	 * Gets the widget contained in this row
	 *
	 * @return SwatWidget the widget contained in this row or null if this
	 *                    row does not contain a widget.
	 */
	public function getWidget()
	{
		return $this->widget;
	}

	// }}}
	// {{{ public function init()

	public function init()
	{
		parent::init();

		if ($this->widget !== null)
			$this->widget->init();
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		parent::process();

		if ($this->widget !== null)
			$this->widget->process();
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id;
		$tr_tag->class = $this->getCSSClassString();

		$colspan = $this->view->getXhtmlColspan();
		$td_tag = new SwatHtmlTag('td');

		$tr_tag->open();

		if ($this->offset > 0 && $this->position === self::POSITION_LEFT)
			$this->displayOffsetCell($this->offset);

		$this->displayWidgetCell();

		if ($this->offset > 0 && $this->position === self::POSITION_RIGHT)
			$this->displayOffsetCell($this->offset);

		$tr_tag->close();
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this row
	 *
	 * If this row has not been displayed, an empty set is returned to reduce
	 * the number of required HTTP requests.
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this row.
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		if ($this->widget !== null) {
			$set->addEntrySet($this->widget->getHtmlHeadEntrySet());
		}

		return $set;
	}

	// }}}
	// {{{ public function getAvailableHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects that may be needed by this row
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
	 *                               needed by this row.
	 */
	public function getAvailableHtmlHeadEntrySet()
	{
		$set = parent::getAvailableHtmlHeadEntrySet();

		if ($this->widget !== null) {
			$set->addEntrySet($this->widget->getAvailableHtmlHeadEntrySet());
		}

		return $set;
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

		return $copy;
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from this table-view-row
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		$messages = array();

		foreach ($this->getDescendants() as $widget) {
			$messages = array_merge($messages, $widget->getMessages());
		}

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets whether or not the widgets in this row have any messages
	 *
	 * @return boolean true if this table-view row has one or more messages
	 *                  and false if it does not.
	 */
	public function hasMessage()
	{
		$has_message = false;
		foreach ($this->getDescendants() as $widget) {
			if ($widget->hasMessage()) {
				$has_message = true;
				break;
			}
		}

		return $has_message;
	}

	// }}}
	// {{{ protected function displayOffsetCell()

	protected function displayOffsetCell($offset)
	{
		$td_tag = new SwatHtmlTag('td');
		$td_tag->class = null;
		$td_tag->colspan = $offset;
		$td_tag->open();
		echo '&nbsp;';
		$td_tag->close();
	}

	// }}}
	// {{{ protected function displayWidgetCell()

	protected function displayWidgetCell()
	{
			$td_tag = new SwatHtmlTag('td');
			$colspan = $this->view->getXhtmlColspan();
			$td_tag->colspan = $colspan - $this->offset;
			$td_tag->class = 'widget-cell';
			$td_tag->open();
			$this->widget->display();
			$td_tag->close();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this row
	 *
	 * @return array the array of CSS classes that are applied to this row.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-table-view-widget-row');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
