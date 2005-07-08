<?php

require_once 'Swat/SwatFlydown.php';

/**
 * A cascading flydown (aka combo-box) selection widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCascadeFlydown extends SwatFlydown
{
	// {{{ public properties

	/**
	 * Flydown options
	 *
	 * An array of parents and options for the flydown in the form:
	 *    array(value1 => title2, value2 => title2).
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Cascade from
	 *
	 * A reference to the {@link SwatWidget} that this item cascades from.
	 *
	 * @var SwatWidget
	 */
	public $cascade_from = null;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this cascading flydown
	 *
	 * {@link SwatFlydown::$show_blank} is set to false here.
	 */
	public function display()
	{
		parent::display();
		$this->displayJavascript();
	}

	// }}}
	// {{{ protected function getOptions()

	/**
	 * Gets the options of this flydown as a flat array
	 *
	 * For the cascading flydown, the array returned 
	 *
	 * The array is of the form:
	 *    value => title
	 *
	 * @return array the options of this flydown as a flat array.
	 *
	 * @see SwatFlydown::getOptions()
	 */
	protected function &getOptions()
	{
		$options = array();

		$parent_value = $this->cascade_from->value;
		if ($parent_value === null) {
			if ($this->cascade_from->show_blank) {
				$options[] = new SwatFlydownOption('', '');
				$options[] = new SwatFlydownOption('', '');
				return $options;
			} else {
				$option_array = $this->options[current($this->cascade_from->options)->value];
			}
		} else
			$option_array = $this->options[$parent_value];

		if ($this->show_blank && count($option_array) > 1)
			$options[] = new SwatFlydownOption('', Swat::_('choose one ...'));

		foreach ($option_array as $value => $title)
			$options[] = new SwatFlydownOption($value, $title);

		return $options;
	}

	// }}}
	// {{{ private function displayjavascript()

	/**
	 * Displays the javascript that makes this control work
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';

		// contains the SwatCascade javascript class.
		include_once 'Swat/javascript/swat-cascade.js';
		
		// TODO: This means an id is mandatory for this widget
		printf("\n {$this->id}_cascade = new SwatCascade('%s', '%s'); ",
			$this->cascade_from->id, $this->id);
	
		foreach($this->options as $parent => $options) {
			if ($this->show_blank && count($options) > 1)
				printf("\n {$this->id}_cascade.addChild('%s', '', '%s');",
                    $parent, Swat::_('choose one ...'));

			foreach ($options as $k => $v) {
				$selected = ($v == $this->value) ? 'true' : 'false';
				printf("\n {$this->id}_cascade.addChild('%s', '%s', '%s', %s);",
					$parent, $k, addslashes($v), $selected);
			}
		}
		echo '</script>';
	}

	// }}}
}

?>
