<?php

/**
 * A an extra row containing a check-all widget
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewCheckAllRow extends SwatTableViewRow
{
	// {{{ public properties

	/**
	 * Optional checkbox label title
	 *
	 * Defaults to "Check All".
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional content type for title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * Count for all items when displaying an extended-all checkbox
	 *
	 * When the check-all checkbox has been checked, an additional
	 * checkbox will appear allowing the user to specify that they wish to
	 * select all possible items. This is useful in cases where pagination
	 * makes selecting all possible items impossible.
	 *
	 * @var integer
	 */
	public $extended_count = 0;

	/**
	 * Count for all visible items when displaying an extended-all checkbox
	 *
	 * @var integer
	 */
	public $visible_count = 0;

	/**
	 * Optional extended-all checkbox unit.
	 *
	 * Used for displaying a "check-all" message. Defaults to "items".
	 *
	 * @var string
	 */
	public $unit;

	/**
	 * The ordinal tab index position of the XHTML input tag
	 *
	 * Values 1 or greater will affect the tab index of this widget. A value
	 * of 0 or null will use the position of the input tag in the XHTML
	 * character stream to determine tab order.
	 *
	 * @var integer
	 */
	public $tab_index;

	// }}}
	// {{{ protected properties

	/**
	 * The check-all widget for this row
	 *
	 * @var SwatCheckAll
	 */
	protected $check_all;

	// }}}
	// {{{ private properties

	/**
	 * The table-view checkbox column to which this check-all row is bound
	 *
	 * @var SwatTableViewCheckboxColumn
	 */
	private $column;

	/**
	 * The identifier of the checkbox list that controls the check-all widget
	 * of this row
	 *
	 * @var string
	 *
	 * @see SwatTableViewCheckAllRow::__construct()
	 */
	private $list_id;

	/**
	 * An internal flag that is set to true when embedded widgets have been
	 * created
	 *
	 * @var boolean
	 *
	 * @see SwatTableViewCheckAllRow::createEmbeddedWidgets()
	 */
	private $widgets_created = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new table-view check-all row
	 *
	 * @param SwatTableViewCheckboxColumn $column the table-view checkbox
	 *                                             column to which this
	 *                                             check-all row is bound.
	 * @param string $list_id the identifier of the checkbox list that controls
	 *                         the check-all widget of this row.
	 */
	public function __construct(SwatTableViewCheckboxColumn $column, $list_id)
	{
		parent::__construct();
		$this->column = $column;
		$this->list_id = $list_id;
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this check-all row
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this check-all row.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$this->createEmbeddedWidgets();

		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->check_all->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ public function getAvailableHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects that may be needed by this
	 * check-all row
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
	 *                               needed by this check-all row.
	 *
	 * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
	 */
	public function getAvailableHtmlHeadEntrySet()
	{
		$this->createEmbeddedWidgets();

		$set = parent::getAvailableHtmlHeadEntrySet();
		$set->addEntrySet($this->check_all->getAvailableHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this check-all row
	 */
	public function init()
	{
		parent::init();
		$this->createEmbeddedWidgets();
		$this->check_all->init();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this check-all row
	 */
	public function process()
	{
		parent::process();
		$this->createEmbeddedWidgets();
		$this->check_all->process();
	}

	// }}}
	// {{{ public function isExtendedSelected()

	/**
	 * Whether or not the extended-checkbox was checked
	 *
	 * @return boolean Whether or not the extended-checkbox was checked
	 */
	public function isExtendedSelected()
	{
		return $this->check_all->isExtendedSelected();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this check-all row
	 */
	public function display()
	{
		if (!$this->visible || count($this->view->model) < 2)
			return;

		parent::display();

		$this->createEmbeddedWidgets();

		$columns = $this->view->getVisibleColumns();

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id;
		$tr_tag->class = $this->getCSSClassString();
		$tr_tag->open();

		// find checkbox column position
		$position = 0;
		foreach ($columns as $column) {
			if ($column === $this->column)
				break;
			else
				$position++;
		}

		if ($position > 0) {
			$td_before_tag = new SwatHtmlTag('td');
			$td_before_tag->setContent('&nbsp;', 'text/xml');
			if ($position > 1)
				$td_before_tag->colspan = $position;

			$td_before_tag->display();
		}

		$td_tag = new SwatHtmlTag('td');
		if (count($columns) - $position > 1)
			$td_tag->colspan = count($columns) - $position;

		$td_tag->open();
		if ($this->title !== null) {
			$this->check_all->title = $this->title;
			$this->check_all->content_type = $this->content_type;
		}

		$this->check_all->extended_count = $this->extended_count;
		$this->check_all->visible_count = $this->visible_count;
		$this->check_all->unit = $this->unit;
		$this->check_all->tab_index = $this->tab_index;
		$this->check_all->display();

		$td_tag->close();

		$tr_tag->close();
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this check-all row
	 *
	 * @return string the inline JavaScript required for this check-all row.
	 *
	 * @see SwatTableViewRow::getInlineJavaScript()
	 */
	public function getInlineJavaScript()
	{
		if (count($this->view->model) < 2)
			return '';

		// set the controller of the check-all widget
		return sprintf("%s_obj.setController(%s);",
			$this->check_all->id, $this->list_id);
	}

	// }}}
	// {{{ private function createEmbeddedWidgets()

	/**
	 * Creates internal widgets required for this check-all row
	 */
	private function createEmbeddedWidgets()
	{
		if (!$this->widgets_created) {
			$this->check_all = new SwatCheckAll();
			$this->check_all->parent = $this;

			$this->widgets_created = true;
		}
	}

	// }}}
}

?>
