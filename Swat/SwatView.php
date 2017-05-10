<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */


/**
 * An abstract class from which to derive recordset views
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
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
	 * @var array
	 *
	 * @deprecated use {@link SwatView::getSelection()} instead.
	 */
	public $checked_items = array();

	// }}}
	// {{{ protected properties

	/**
	 * The selections of this view
	 *
	 * This is an array of {@link SwatViewSelection} objects indexed by
	 * selector id.
	 *
	 * @var array
	 */
	protected $selections = array();

	/**
	 * The selectors of this view
	 *
	 * This is an array of {@link SwatViewSelector} objects indexed by selector
	 * id.
	 *
	 * @var array
	 */
	protected $selectors = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new recordset view
	 *
	 * @param string $id a non-visible unique id for this recordset view.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$yui = new SwatYUI(array('dom'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-view.js');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this view
	 */
	public function init()
	{
		parent::init();

		// add selectors of this view but not selectors of sub-views
		$selectors = $this->getDescendants('SwatViewSelector');
		foreach ($selectors as $selector)
			if ($selector->getFirstAncestor('SwatView') === $this)
				$this->addSelector($selector);
	}

	// }}}
	// {{{ public function getSelection()

	/**
	 * Gets a selection of this view
	 *
	 * Selections are an iterable, countable set of row identifiers for rows
	 * processed in this view that were selected (in some way) by the user.
	 *
	 * @param SwatViewSelector|string $selector optional. The view selector
	 *                                           object or the view selector
	 *                                           identifier for which to get
	 *                                           the selection. Use this
	 *                                           parameter if this view has
	 *                                           multiple selectors. By default,
	 *                                           the first selector in the view
	 *                                           is used.
	 *
	 * @return SwatViewSelection the selection of this view for the specified
	 *                            selector.
	 *
	 * @throws SwatObjectNotFoundException if the <i>$selector</i> parameter is
	 *                                     specified as a string and this view
	 *                                     does not contain a selector with the
	 *                                     given identifier.
	 * @throws SwatInvalidClassException if the <i>$selector</i> parameter is
	 *                                   specified as an object that is not a
	 *                                   {@link SwatViewSelector}.
	 * @throws SwatException if the <i>$selector</i> parameter is specified as
	 *                       a SwatViewSelector but the selector does not
	 *                       belong to this view.
	 * @throws SwatException if the <i>$selector</i> parameter is specified and
	 *                       this view has no selectors.
	 */
	public function getSelection($selector = null)
	{
		if ($selector === null) {
			if (count($this->selectors) > 0)
				$selector = reset($this->selectors);
			else
				throw new SwatException(
					'This view does not have any selectors.');
		} elseif (is_string($selector)) {
			if (isset($this->selectors[$selector]))
				$selector = $this->selectors[$selector];
			else
				throw new SwatObjectNotFoundException('Selector with an id '.
					"of {$selector} does not exist in this view.", 0,
					$selector);
		} elseif (!($selector instanceof SwatViewSelector)) {
			throw new SwatInvalidClassException('Specified object is not '.
				'a SwatViewSelector object.', 0, $selector);
		} elseif (!isset($this->selections[$selector->getId()])) {
			throw new SwatException(
				'Specified SwatViewSelector is not a selector of this view.');
		}

		return $this->selections[$selector->getId()];
	}

	// }}}
	// {{{ public function setSelection()

	/**
	 * Sets a selection of this view
	 *
	 * Use by {@link SwatViewSelector} objects during the processing phase to
	 * set the selection of this view for a particular selector.
	 *
	 * This method may also be used to override the selection provided by a
	 * selector.
	 *
	 * @param SwatViewSelection $selection the selection object to set.
	 * @param SwatViewSelector|string $selector optional. The view selector
	 *                                           object or the view selector
	 *                                           identifier for which to get
	 *                                           the selection. Use this
	 *                                           parameter if this view has
	 *                                           multiple selectors. By default,
	 *                                           the first selector in the view
	 *                                           is used.
	 *
	 * @throws SwatObjectNotFoundException if the <i>$selector</i> parameter is
	 *                                     specified as a string and this view
	 *                                     does not contain a selector with the
	 *                                     given identifier.
	 * @throws SwatInvalidClassException if the <i>$selector</i> parameter is
	 *                                   specified as an object that is not a
	 *                                   {@link SwatViewSelector}.
	 * @throws SwatException if the <i>$selector</i> parameter is specified as
	 *                       a SwatViewSelector but the selector does not
	 *                       belong to this view.
	 * @throws SwatException if the <i>$selector</i> parameter is specified and
	 *                       this view has no selectors.
	 */
	public function setSelection(SwatViewSelection $selection, $selector = null)
	{
		if ($selector === null) {
			if (count($this->selectors) > 0)
				$selector = reset($this->selectors);
			else
				throw new SwatException(
					'This view does not have any selectors.');
		} elseif (is_string($selector)) {
			if (isset($this->selectors[$selector]))
				$selector = $this->selectors[$selector];
			else
				throw new SwatObjectNotFoundException('Selector with an id '.
					"of {$selector} does not exist in this view.", 0,
					$selector);
		} elseif (!($selector instanceof SwatViewSelector)) {
			throw new SwatInvalidClassException('Specified object is not '.
				'a SwatViewSelector object.', 0, $selector);
		} elseif (!isset($this->selections[$selector->getId()])) {
			throw new SwatException(
				'Specified SwatViewSelector is not a selector of this view.');
		}

		$this->selections[$selector->getId()] = $selection;
	}

	// }}}
	// {{{ protected final function addSelector()

	/**
	 * This method should be called internally by the
	 * {@link SwatView::init() method on all descendant UI-objects that are
	 * SwatViewSelector objects.
	 */
	protected final function addSelector(SwatViewSelector $selector)
	{
		$this->selections[$selector->getId()] = new SwatViewSelection(array());
		$this->selectors[$selector->getId()] = $selector;
	}

	// }}}
}

?>
