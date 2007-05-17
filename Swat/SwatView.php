<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';

/**
 * An abstract class to derive views from
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatView extends SwatControl
{
	// {{{ public properties

	/**
	 * A data structure that holds the data to display in this view
	 *
	 * The data structure used is some form of {@link SwatTableModel}.
	 *
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * The values of the checked checkboxes
	 *
	 * This array is set in the {@link SwatTableView::process()} method. For
	 * this to be set, this table-view must contain a
	 * {@link SwatCellRendererCheckbox} with an id of "checkbox".
	 *
	 * TODO: Make this private with an accessor method
	 *
	 * @var array
	 */
	public $checked_items = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new view
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
	}

	// }}}
}

?>
