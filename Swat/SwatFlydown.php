<?php

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatFlydownOption.php';
require_once 'Swat/SwatFlydownDivider.php';

/**
 * A flydown (aka combo-box) selection widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFlydown extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Flydown options
	 *
	 * An array of {@link SwatFlydownOptions}
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Flydown value
	 *
	 * The index value of the selected option, or null if no option is
	 * selected.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Show a blank option
	 *
	 * Whether or not to show a blank value at the top of the flydown.
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Blank title
	 *
	 * The user visible title to display in the blank field.
	 *
	 * @var string
	 */
	public $blank_title = '';

	/**
	 * On change
	 *
	 * The onchange attribute of the XHTML select tag, or null.
	 *
	 * @var string
	 */
	public $onchange = null;

	/**
	 * Width
	 *
	 * The visible width of the select tag. Can be defined in percentage, ems,
	 * or pixels.
	 *
	 * @var string
	 */
	public $width = null;

	// }}}
	// {{{ public function display()
	
	/**
	 * Displays this flydown
	 *
	 * Displays this flydown as a XHTML select.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$options = $this->getOptions();

		// Empty string XHTML option value is assumed to be null
		// when processing.
		if ($this->show_blank)
			$options = array_merge(
				array(new SwatFlydownOption('', $this->blank_title)),
				$options);

		// only show a select if there is more than one option
		if (count($options) > 1) {
			
			$select_tag = new SwatHtmlTag('select');
			$select_tag->name = $this->id;
			$select_tag->id = $this->id;

			if ($this->width !== null)
				$select_tag->style = 'width: '.$this->width.';';

			if ($this->onchange !== null)
				$select_tag->onchange = $this->onchange;

			$option_tag = new SwatHtmlTag('option');

			$select_tag->open();

			foreach ($options as $flydown_option) {
				if ($flydown_option instanceof SwatFlydownDivider) {
					$optgroup_tag = new SwatHtmlTag('optgroup');
					$optgroup_tag->label = $flydown_option->title;
					$optgroup_tag->class = 'swat-flydown-divider';
					$optgroup_tag->content = '';
					$optgroup_tag->display();
				} else {
					$option_tag->value = (string)$flydown_option->value;
					$option_tag->removeAttribute('selected');

					if ((string)$this->value === (string)$flydown_option->value)
						$option_tag->selected = 'selected';

					$option_tag->content = $flydown_option->title;

					$option_tag->display();
				}
			}

			$select_tag->close();

		} elseif (count($options) == 1) {

			// get first and only element
			$flydown_option = current($options);
			$title = $flydown_option->title;
			$value = $flydown_option->value;

			$hidden_tag = new SwatHtmlTag('input');
			$hidden_tag->type = 'hidden';
			$hidden_tag->name = $this->id;
			$hidden_tag->value = (string)$flydown_option->value;

			$hidden_tag->display();

			echo $title;
		}
	}

	// }}}
	// {{{ public function process()

	/**
	 * Figures out what option was selected
	 *
	 * Processes this widget and figures out what select element from this
	 * flydown was selected. Any validation errors cause an error message to
	 * be attached to this widget in this method.
	 */
	public function process()
	{
		$value = $_POST[$this->id];

		// Empty string HTML option value is considered to be null.
		if (strlen($value) == 0)
			$this->value = null;
		else
			$this->value = $value;

		if ($this->required && $this->value === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}

	// }}}
	// {{{ public function addOption()

	/**
	 * Add an option element
	 *
	 * @param mixed $value Either a simply value for the option, or a
	 *                      {@link SwatFlydownOption} object. If a
	 *                      {@link SwatFlydownOption} object is used, the
	 *                      $title parameter of addOption will be ignored.
	 * @param string $title The title of the option element.
	 */
	public function addOption($value, $title = '')
	{
		if ($value instanceof SwatFlydownOption)
			$this->options[] = $value;
		else
			$this->options[] = new SwatFlydownOption($value, $title);
	}

	// }}}
	// {{{ public function addDivider()

	/**
	 * Adds a divider to this flydown
	 *
	 * A divider is an unselectable flydown option.
	 *
	 * @param string $title the title of the divider. Defaults to two em
	 *                       dashes.
	 */
	public function addDivider($title = '&#8212;&#8212;')
	{
		$this->options[] = new SwatFlydownDivider('', $title);
	}

	// }}}
	// {{{ public function addOptionsByArray()

	/**
	 * Add an option element
	 *
	 * @param array $options An associative array of options.
	 */
	public function addOptionsByArray($options)
	{
		foreach ($options as $value => $title)
			$this->addOption($value, $title);
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Resets this flydown
	 *
	 * Resets this flydown to its default state. This method is useful to
	 * call from a display() method when form persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->value = null;
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this flydown
	 *
	 * @return boolean the current state of this flydown.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this flydown
	 *
	 * @param boolean $state the new state of this flydown.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	// }}}
	// {{{ protected function getOptions()

	/**
	 * Gets a reference to the array of options to show in this flydown
	 *
	 * Subclasses may want to override this method.
	 *
	 * @return array a reference to the array of options to show in this
	 *                flydown.
	 */
	protected function &getOptions()
	{
		return $this->options;
	}

	// }}}
}

?>
