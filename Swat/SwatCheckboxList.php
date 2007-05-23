<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatYUI.php';

/**
 * A checkbox list widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxList extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Checkbox list options
	 *
	 * An array of options for the radio list in the form value => title.
	 *
	 * @var array
	 */
	public $options = null;
	//TODO: shouldn't this use addOptionsByArray() like flydown?

	/**
	 * Optional content type for option titles
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * List values 
	 *
	 * The values of the selected items.
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML input type=checkbox tags, or null.
	 *
	 * @var string
	 */
	public $onchange = null;

	// }}}
	// {{{ protected properties

	/**
	 * The check-all widget for this list
	 *
	 * The check-all is displayed if the list has more than one checkable
	 * item and is an easy way for users to check/uncheck the entire list.
	 *
	 * @var SwatCheckAll
	 */
	protected $check_all = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new checkbox list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;
		$this->check_all = new SwatCheckAll();
		$this->check_all->parent = $this;
		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addJavaScript('packages/swat/javascript/swat-checkbox-list.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this checkbox list
	 */
	public function init()
	{
		parent::init();
		$this->check_all->init();
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this checkbox list 
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this checkbox list.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->check_all->getHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this checkbox list
	 *
	 * The check-all widget is only displayed if more than one checkable item
	 * is displayed.
	 */
	public function display()
	{
		if (!$this->visible || $this->options === null)
			return;

		$this->getForm()->addHiddenField($this->id.'_submitted', 1);

		// outer div is required because the check-all widget is outside the
		// unordered list
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'[]';
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		echo '<ul>';

		foreach ($this->options as $value => $title) {

			echo '<li>';

			$input_tag->value = (string)$value;
			$input_tag->removeAttribute('checked');

			if (in_array($value, $this->values))
				$input_tag->checked = 'checked';

			$input_tag->id = $this->id.'_'.$input_tag->value;
			$input_tag->display();

			$label_tag->for = $this->id.'_'.$input_tag->value;
			$label_tag->setContent($title, $this->content_type);
			$label_tag->display();

			echo '</li>';
		}

		echo '</ul>';

		// Only show the check all control if more than one checkable item is
		// displayed.
		$this->check_all->visible = (count($this->options) > 1);
		$this->check_all->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this checkbox list widget
	 *
	 * @return array Array of checked values
	 */
	public function process()
	{
		$form = $this->getForm();

		if ($form->getHiddenField($this->id.'_submitted') === null)
			return;

		parent::process();

		$this->check_all->process();

		$data = &$form->getFormData();

		if (isset($data[$this->id]))
			$this->values = $data[$this->id];
		else
			$this->values = array();
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Reset this checkbox list.
	 *
	 * Reset the list to its default state. This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->values = key($this->options);
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this checkbox list 
	 *
	 * @param array $state the new state of this checkbox list.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->values = $state;
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this checkbox list 
	 *
	 * @return array the current state of this checkbox list.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->values;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this checkbox list
	 *
	 * @return string the inline JavaScript for this checkbox list.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf("var %s_obj = new SwatCheckboxList('%s');",
			$this->id, $this->id);

		// set check-all controller if it is visible
		if ($this->check_all->visible)
			$javascript.= sprintf("\n%s_obj.setController(%s_obj);",
				$this->check_all->id, $this->id);

		return $javascript;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this checkbox list
	 *
	 * @return array the array of CSS classes that are applied to this checkbox
	 *                list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-checkbox-list');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
