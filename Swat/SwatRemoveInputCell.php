<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputCell.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatContentBlock.php';
require_once 'Swat/SwatWidget.php';
require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * An input cell containing a "remove row" link
 *
 * One or more of these input cell are required if you want the user to be
 * able to remove rows from the table-view's input-row. For example if the
 * user accidentally adds 10 input rows but only wants to submit 3 you need to
 * add a SwatRemoveInputCell object to one or more columns so the user can
 * remove the extra rows before submitting the form.
 *
 * This input cell is automatically assigned a widget when it is initialized.
 * Trying to set the widget for this cell will result in an exception being
 * thrown.
 *
 * @package   Swat
 * @copyright 2006-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRemoveInputCell extends SwatInputCell
{
	// {{{ public function init()

	/**
	 * Sets the remove widget for this input cell
	 *
	 * In SwatRemoveInputCell objects the remove widget is automatically set to
	 * a SwatContentBlock with predefined content for the remove link.
	 *
	 * @throws SwatException
	 */
	public function init()
	{
		$row = $this->getInputRow();
		if ($row === null)
			throw new SwatException('Remove input-cells can only be used '.
				'inside table-views with an input-row.');

		$content = new SwatContentBlock();

		ob_start();

		$view = $this->getFirstAncestor('SwatTableView');
		$view_id = ($view === null) ? null : $view->id;
		$id = ($view_id === null) ? $row->id : $view_id.'_'.$row->id;

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->class = 'swat-remove-input-cell-remove';
		$anchor_tag->href =
			sprintf("javascript:%s_obj.removeRow('%%s');", $id);

		$anchor_tag->setContent(Swat::_('Remove this row'));
		$anchor_tag->display();

		$content->content  = ob_get_clean();
		$content->content_type = 'text/xml';

		// manually set the widget since setWidget() is over-ridden to throw
		// an exception.
		$this->widget = $content;
		$content->parent = $this;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this remove input cell given a numeric row identifier
	 *
	 * @param integer $replicator_id the numeric identifier of the input row
	 *                                that is being displayed.
	 *
	 * @see SwatInputCell::display()
	 */
	public function display($replicator_id)
	{
		$widget = $this->getClonedWidget($replicator_id);
		// substitute the replicator_id into the content block's contents
		$widget->content = str_replace('%s', $replicator_id, $widget->content);
		$widget->display();
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 * Sets the widget of this input cell
	 *
	 * SwatRemoveInputCell objects cannot have their widget set manually so
	 * this method is over-ridden to always throw an exception.
	 *
	 * @param SwatWidget $widget the new widget of this input cell.
	 *
	 * @throws SwatException
	 */
	public function setWidget(SwatWidget $child)
	{
		throw new SwatException('Remove input cells must be empty');
	}

	// }}}
}

?>
