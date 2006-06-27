<?php

require_once 'Swat/SwatFlydown.php';

/**
 * A cascading flydown (aka combo-box) selection widget
 *
 * The term cascading refers to the fact that this flydown's contents are
 * updated dynamically based on the selected value of another flydown.
 *
 * The value of the other SwatFlydown cascades to this SwatCascadeFlydown.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCascadeFlydown extends SwatFlydown
{
	// {{{ public properties

	/**
	 * Flydown options
	 *
	 * An array of parents and {@link SwatOption}s for the flydown. Each parent
	 * value is associated to an array of possible child
	 * values, in the form:
	 *    array(
	 *        parent_value1 => array(SwatOption1, SwatOption2),
	 *        parent_value2 => array(SwatOption3, SwatOption4),
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
	// {{{ public function __construct()

	/**
	 * Creates a new calendar
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-cascade.js');
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
		if (!$this->visible)
			return;

		parent::display();
		$this->displayJavaScript();
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
				$ret[] = new SwatOption('', '');
				$ret[] = new SwatOption('', '');
				return $ret;
			} else {
				$current = current($this->cascade_from->options)->value;
				$option_array = $this->options[$current];
			}
		} else
			$option_array = $this->options[$parent_value];

		if ($this->show_blank && count($option_array) > 1) {
			unset($ret[key($ret)]);
			$ret[] = new SwatOption('', Swat::_('choose one ...'));
		}

		$ret = array_merge($ret, $option_array);
		return $ret;
	}

	// }}}
	// {{{ private function displayjavascript()

	/**
	 * Displays the JavaScript that makes this control work
	 */
	private function displayJavaScript()
	{
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
