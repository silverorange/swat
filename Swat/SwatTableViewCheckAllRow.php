<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTableViewRow.php';
require_once 'Swat/SwatCheckAll.php';

/**
 * A an extra row containing a check-all widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
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
	public $title = null;

	/**
	 * Optional content type for title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	// }}}
	// {{{ private properties

	/**
	 * The unique identifier of the checkbox column that this check-all row
	 * is bound to
	 *
	 * @var string
	 */
	private $column_id;

	/**
	 * The check-all widget for this row 
	 *
	 * @var SwatCheckAll
	 */
	private $check_all;

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
	 * Creates a new check-all row
	 *
	 * @param string $column_id the unique identifier of the checkbox column
	 *                           that this check-all row is bound to.
	 */
	public function __construct($column_id)
	{
		parent::__construct();
		$this->column_id = $column_id;
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
	// {{{ public function display()

	/**
	 * Displays this check-all row
	 */
	public function display()
	{
		if (!$this->visible || $this->view->model->getRowCount() < 2)
			return;

		$this->createEmbeddedWidgets();

		$columns = $this->view->getVisibleColumns();

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id;
		$tr_tag->class = $this->getCSSClassString();
		$tr_tag->open();

		// find checkbox column position
		$position = 0;
		foreach ($columns as $column)
			if ($column->id == $this->column_id)
				break;
			else
				$position++;

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
		$this->check_all->display();
		$td_tag->close();

		$tr_tag->close();
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this row
	 *
	 * @return string the inline JavaScript required for this row.
	 *
	 * @see SwatTableViewRow::getInlineJavaScript()
	 */
	public function getInlineJavaScript()
	{
		if ($this->view->model->getRowCount() < 2)
			return '';

		// set the controller of the check-all widget
		return sprintf("%s_obj.setController(%s);",
			$this->check_all->id, $this->column_id);
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
