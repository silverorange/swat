<?php

/**
 * A view selector cell renderer displayed as a checkbox
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatViewSelector
 */
class SwatCheckboxCellRenderer extends SwatCellRenderer
	implements SwatViewSelector
{

	/**
	 * Identifier of this checkbox cell renderer
	 *
	 * Identifier must be unique within this cell renderer's parent cell
	 * renderer container. This property is required and can not be a
	 * data-mapped value.
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
			'packages/swat/javascript/swat-checkbox-cell-renderer.js'
		);

		// auto-generate an id to use if no id is set
		$this->id = $this->getUniqueId();
	}

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

	/**
	 * Renders this checkbox cell renderer
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		parent::render();

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
		$checkbox_tag->tabindex = $this->tab_index;

		if (!$this->sensitive)
			$checkbox_tag->disabled = 'disabled';

		$view = $this->getFirstAncestor('SwatView');
		if ($view !== null) {
			$selection = $view->getSelection($this);
			if ($selection->contains($this->value))
				$checkbox_tag->checked = 'checked';
		}

		echo '<span class="swat-checkbox-wrapper">';
		$checkbox_tag->display();
		echo '<span class="swat-checkbox-shim"></span>';
		echo '</span>';

		if ($this->title !== null) {
			$label_tag->displayContent();
			$label_tag->close();
		}
	}

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

		if ($id_suffix != '')
			$copy->id = $copy->id.$id_suffix;

		return $copy;
	}

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

}

?>
