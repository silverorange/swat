<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatViewSelector.php';
require_once 'Swat/SwatViewSelection.php';
require_once 'Swat/SwatCheckbox.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * A view selector cell renderer displayed as a checkbox
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatViewSelector
 */
class SwatCheckboxCellRenderer extends SwatCellRenderer
	implements SwatViewSelector
{
	// {{{ public properties

	/**
	 * Identifier of this checkbox cell renderer
	 *
	 * Identifier must be unique within this cell renderer's parent cell
	 * renderer container. This property is required and must be a static
	 * value, not a data-mapped value.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Value of this cell's checkbox
	 *
	 * This property is intended to be data-mapped to the current row
	 * identifier in a record set.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Optional title of the label for the rendered checkbox
	 *
	 * If no title is specified (default) there is no label displayed with
	 * the checkbox.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional content type for checkbox label title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	// }}}
	// {{{ private properties

	/**
	 * Array of selected values populated during the processing of this cell
	 * renderer
	 *
	 * This property is used to track the selected state of checkboxes when
	 * rendering for a particular value.
	 *
	 * @var array
	 */
	private $selected_values = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new checkbox cell renderer
	 */
	public function __construct()
	{
		parent::__construct();

		$this->makePropertyStatic('id');

		$yui = new SwatYUI(array('dom'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript(
			'packages/swat/javascript/swat-checkbox-cell-renderer.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this checkbox cell renderer
	 */
	public function process()
	{
		$form = $this->getForm();
		if ($form !== null && $form->isSubmitted()) {
			$data = $form->getFormData();
			if (isset($data[$this->id]) && is_array($data[$this->id])) {
				$this->selected_values = $data[$this->id];

				$view = $this->getFirstAncestor('SwatView');
				if ($view !== null) {
					$selection = new SwatViewSelection($this->selected_values);
					$view->setSelection($selection, $this);
				}
			}
		}
	}

	// }}}
	// {{{ public function render()

	/**
	 * Renders this checkbox cell renderer
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		if ($this->title !== null) {
			$label_tag = new SwatHtmlTag('label');
			$label_tag->for = $this->id.'_checkbox_'.$this->value;
			$label_tag->setContent($this->title, $this->content_type);
			$label_tag->open();
		}

		$checkbox_tag = new SwatHtmlTag('input');
		$checkbox_tag->type = 'checkbox';
		$checkbox_tag->name = $this->id.'[]';
		$checkbox_tag->id = $this->id.'_checkbox_'.$this->value;
		$checkbox_tag->value = $this->value;
		if (!$this->sensitive)
			$checkbox_tag->disabled = 'disabled';

		if (in_array($this->value, $this->selected_values))
			$checkbox_tag->checked = 'checked';

		$checkbox_tag->display();

		if ($this->title !== null) {
			$label_tag->displayContent();
			$label_tag->close();
		}
	}

	// }}}
	// {{{ public function getId()

	/**
	 * Gets the identifier of this checkbox cell renderer
	 *
	 * Satisfies the {@link SwatViewSelector} interface.
	 *
	 * @return string the identifier of this checkbox cell renderer.
	 */
	public function getId()
	{
		return $this->id;
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this checkbox cell renderer
	 *
	 * @return string the inline JavaScript required by this checkbox cell
	 *                 renderer.
	 */
	public function getInlineJavaScript()
	{
		$view = $this->getFirstAncestor('SwatView');
		if ($view !== null) {
			$javascript = sprintf(
				"var %s = new SwatCheckboxCellRenderer('%s', %s);",
				$this->id, $this->id, $view->id);
		} else {
			$javascript = '';
		}

		return $javascript;
	}

	// }}}
	// {{{ private function getForm()

	/**
	 * Gets the form this checkbox cell renderer is contained in
	 *
	 * @return SwatForm the form this checkbox cell renderer is contained in.
	 *
	 * @throws SwatException if this checkbox cell renderer does not have a
	 *                       SwatForm ancestor.
	 */
	private function getForm()
	{
		$form = $this->getFirstAncestor('SwatForm');

		if ($form === null)
			throw new SwatException('SwatCheckboxCellRenderer must have '.
				'a SwatForm ancestor in the UI tree.');

		return $form;
	}

	// }}}
}

?>
