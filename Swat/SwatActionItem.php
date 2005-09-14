<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatUIParent.php';

/**
 * A single entry in a SwatActions widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatActions
 */
class SwatActionItem extends SwatControl implements SwatUIParent
{
	/**
	 * A unique identifier for this action item
	 *
	 * @var string
	 */
	public $id;

	/**
	 * A human readable title displayed for this item
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * A SwatWidget associated with this action item
	 *
	 * @var SwatWidget
	 */
	public $widget = null;

	/**
	 * Creates a new item for a SwatActions widget
	 *
	 * @param string $id a unique identifier for this item
	 */
	public function __construct($id = '')
	{
		$this->id = $id;
	}

	/**
	 * Initializes this action item
	 *
	 * This initializes the widget contained in this action item if there is
	 * one.
	 */
	public function init()
	{
		parent::init();

		if ($this->widget !== null)
			$this->widget->init();
	}

	/**
	 * Displays this item
	 *
	 * Calls this item's widget display method.
	 */
	public function display()
	{
		$this->widget->display();
	}

	/**
	 * Sets the widget to use for this item
	 *
	 * Each SwatActionItem can have one associated SwatWidget. This method
	 * sets the widget for this item.
	 *
	 * @param SwatWidget $widget the widget associated with this action.
	 *
	 * @throws SwatException
	 */
	public function setWidget(SwatWidget $widget)
	{
		if ($this->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an SwatActionItem');

		$this->widget = $widget;
	}

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To set the a widget in an action item, use 
	 * {@link SwatActionItem::setWidget()}.
	 *
	 * @param $child a reference to a child object to add.
	 *
	 * @throws SwatException
	 *
	 * @see SwatUIParent, SwatUI, SwatActionItem::setWidget()
	 */
	public function addChild($child)
	{
		if ($child instanceof SwatWidget)
			$this->setWidget($child);
		else
			throw new SwatException('SwatActionItem: Only '.
				'SwatWidgets can be nested within SwatActionItem');
	}

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this action item
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this action item.
	 *
	 * @see SwatWidget::gatherSwatHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;

		if ($this->widget !== null)
			$out = array_merge($out, $this->widget->getHtmlHeadEntries());

		return $out;
	}
}

?>
