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
	 * An array of parents and {@link SwatFlydownOption}s for the flydown. Each parent value
	 * is associated to an array of possible child values, in the form:
	 *    array(
	 *	parent_value1 => array(SwatFlydownOption1, SwatFlydownOption2),
	 *	parent_value2 => array(SwatFlydownOption3, SwatFlydownOption4),
	 *    )
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
	// {{{ public function init()

	/**
	 * Initializes this cascading flydown widget
	 *
	 * Cascading flydown widgets need to have id's set.
	 */
	public function init()
	{
		// an id is required for this widget.
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}

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
		$ret = array();

		$parent_value = $this->cascade_from->value;
		if ($parent_value === null) {
			if ($this->cascade_from->show_blank) {
				$ret[] = new SwatFlydownOption('', '');
				$ret[] = new SwatFlydownOption('', '');
				return $ret;
			} else
				$option_array = $this->options[current($this->cascade_from->options)->value];

		} else
			$option_array = $this->options[$parent_value];

		if ($this->show_blank && count($option_array) > 1) {
			unset($ret[key($ret)]);
			$ret[] = new SwatFlydownOption('', Swat::_('choose one ...'));
		}

		return array_merge($ret, $option_array);
	}

	// }}}
	// {{{ private function displayjavascript()

	/**
	 * Displays the javascript that makes this control work
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript" src="swat/javascript/swat-cascade.js"></script>';

		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		printf("\n {$this->id}_cascade = new SwatCascade('%s', '%s'); ",
			$this->cascade_from->id, $this->id);
	
		foreach($this->options as $parent => $options) {
			if ($this->show_blank && count($options) > 1)
				printf("\n {$this->id}_cascade.addChild('%s', '', '%s');",
                    			$parent, Swat::_('choose one ...'));

			foreach ($options as $option) {
				$selected = ($option->value == $this->value) ? 'true' : 'false';
				printf("\n {$this->id}_cascade.addChild('%s', '%s', '%s', %s);",
					$parent, $option->value, addslashes($option->title), $selected);
			}
		}

		echo "\n//]]>";
		echo '</script>';
	}

	// }}}
}

?>
