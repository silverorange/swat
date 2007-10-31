<?php

require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/SwatUIParent.php';

/**
 * A table view row with an embedded widget
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
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
	 * How far from the right side the table the widget should be displayed
	 * measured in columns
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
	 * By default, the vutton displays in the left cell. Use the POSITION_*
	 * constants to control the widget position.
	 *
	 * @var integer
	 */
	public $position = self::POSITION_LEFT;

	// }}}
	// {{{ protected properties

	/**
	 * Whether or not the internal widgets used by this row have been created
	 * or not
	 *
	 * @var boolean
	 */
	protected $widgets_created = false;

	/**
	 * The embedded widget
	 *
	 * @var SwatButton
	 */
	protected $widget = null;

	// }}}
	// {{{ public function addChild()

	/**
	 * Fulfills SwatUIParent::addChild()
	 *
	 * @throws SwatException
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->widget === null)
			$this->setWidget($child);
		else
			throw new SwatException(
				'Can only add one widget to a widget cell renderer');
	}

	// }}}
	// {{{ public function getDescendants()

	public function getDescendants($class_name = null)
	{
		$out = array();

		if ($this->widget instanceof SwatUIParent)
			$out = $this->widget->getDescendants($class_name);

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	public function getFirstDescendant($class_name)
	{
		$out = null;

		if ($this->widget instanceof SwatUIParent)
			$out = $this->widget->getFirstDescendant($class_name);

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	public function getDescendantStates()
	{
		$out = null;

		if ($this->widget instanceof SwatUIParent)
			$this->widget->getDescendantStates();

		return $out;
	}

	// }}}
	// {{{ public function setDescendantStates()

	public function setDescendantStates(array $states)
	{
		if ($this->widget instanceof SwatUIParent)
			$this->widget->setDescendantStates($states);
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 *
	 * @param SwatWidget $widget
	 */
	public function setWidget(SwatWidget $widget)
	{
		$this->widget = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ public function getWidget()

	/**
	 *
	 * @return SwatWidget the embedded widget.
	 */
	public function getWidget()
	{
		return $this->widget;
	}

	// }}}
	// {{{ public function init()

	public function init()
	{
		$this->widget->init();
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		$this->widget->process();
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id;
		$tr_tag->class = $this->getCSSClassString();

		$colspan = $this->view->getXhtmlColspan();
		$td_tag = new SwatHtmlTag('td');
		$td_tag->colspan = $colspan - $this->offset;

		$tr_tag->open();

		if ($this->position === self::POSITION_LEFT || $this->offset == 0) {
			$td_tag->class = 'widget-cell';
			$td_tag->open();
			$this->widget->display();
			$td_tag->close();
		} else {
			$td_tag->open();
			echo '&nbsp;';
			$td_tag->close();
		}

		if ($this->offset > 0) {
			$td_tag->colspan = $this->offset;

			if ($this->position === self::POSITION_RIGHT) {
				$td_tag->class = 'widget-cell';
				$td_tag->open();
				$this->widget->display();
				$td_tag->close();
			} else {
				$td_tag->open();
				echo '&nbsp;';
				$td_tag->close();
			}
		}

		$tr_tag->close();
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->widget->getHtmlHeadEntrySet());
		return $set;
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
