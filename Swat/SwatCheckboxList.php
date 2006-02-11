<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatString.php';

/**
 * A checkbox list widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxList extends SwatControl implements SwatState
{
	/**
	 * Checkbox list options
	 *
	 * An array of options for the radio list in the form value => title.
	 *
	 * @var array
	 */
	public $options = null;

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

	/**
	 * The check-all widget for this list
	 *
	 * The check-all is displayed if the list has more than one checkable
	 * item and is an easy way for users to check/uncheck the entire list.
	 *
	 * @var SwatCheckAll
	 */
	protected $check_all = null;

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
		$this->addJavaScript('swat/javascript/swat-checkbox-list.js');
	}

	/**
	 * Initializes this checkbox list
	 */
	public function init()
	{
		parent::init();
		$this->check_all->init();
	}

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this checkbox list 
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this checkbox list.
	 *
	 * @see SwatUIObject::getSwatHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;
		$out = array_merge($out, $this->check_all->getHtmlHeadEntries());
		return $out;
	}

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

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-checkbox-list';
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

		$this->displayJavaScript();

		$div_tag->close();
	}

	/**
	 * Processes this checkbox list widget
	 *
	 * @return array Array of checked values
	 */
	public function process()
	{
		$this->check_all->process();

		if (isset($_POST[$this->id]))
			$this->values = $_POST[$this->id];
		else
			$this->values = array();
	}

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

	public function setState($state)
	{
		$this->values = $state;
	}

	public function getState()
	{
		return $this->values;
	}

	/**
	 * Displays the JavaScript for this checkbox list
	 */
	protected function displayJavaScript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		printf("var %s_obj = new SwatCheckboxList('%s');\n",
			$this->id, $this->id);

		// set check-all controller if it is visible
		if ($this->check_all->visible)
			printf("%s_obj.setController(%s_obj);\n",
				$this->check_all->id, $this->id);

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
