<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatOption.php';

/**
 * A base class for controls using a set of options
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatOptionControl extends SwatInputControl
{
	// {{{ public properties

	/**
	 * Options
	 *
	 * An array of {@link SwatOptions}
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Whether or not to serialize option values
	 *
	 * If option values are serialized, the PHP type is remembered between
	 * page loads. This is useful if, for example, your option values are a mix
	 * of strings, integers or null values. You can also use complex objects as
	 * option values if this property is set to <i>true</i>.
	 *
	 * If this property is set to <i>false</i>, the values are always converted
	 * to strings. This is most useful for SwatForms using the GET method but
	 * could be applicable in other circumstances.
	 *
	 * @var boolean
	 */
	public $serialize_values = true;

	// }}}
	// {{{ public function addOption()

	/**
	 * Adds an option to this option control
	 *
	 * @param mixed|SwatOption $value either a value for the option, or a
	 *                                 {@link SwatOption} object. If a
	 *                                 SwatOption is used, the <i>$title</i>
	 *                                 and <i>$content_type</i> paramaters of
	 *                                 this method call are ignored.
	 * @param string $title the title of the added option. Ignored if the
	 *                       <i>$value</i> parameter is a SwatOption object.
	 * @param string $content_type optional. The content type of the title. If
	 *                              not specified, defaults to 'text/plain'.
	 *                              Ignored if the <i>$value</i> paramater is
	 *                              a SwatOption object.
	 */
	public function addOption($value, $title = '', $content_type = 'text/plain')
	{
		if ($value instanceof SwatOption)
			$option = $value;
		else
			$option = new SwatOption($value, $title, $content_type);

		$this->options[] = $option;
	}

	// }}}
	// {{{ public function removeOption()

	/**
	 * Removes an option from this option control
	 *
	 * @param SwatOption $option the option to remove.
	 *
	 * @return SwatOption the removed option or null if no option was removed.
	 */
	public function removeOption(SwatOption $option)
	{
		$removed_option = null;

		foreach ($this->options as $key => $control_option) {
			if ($control_option === $option) {
				$removed_option = $control_option;
				unset($this->options[$key]);
			}
		}

		return $removed_option;
	}

	// }}}
	// {{{ public function removeOptionsByValue()

	/**
	 * Removes options from this option control by their value
	 *
	 * @param mixed $value the value of the option or options to remove.
	 *
	 * @return array an array of removed SwatOption objects or an empty array
	 *                if no options are removed.
	 */
	public function removeOptionsByValue($value)
	{
		$removed_options = array();

		foreach ($this->options as $key => $control_option) {
			if ($control_option->value === $value) {
				$removed_options[] = $control_option;
				unset($this->options[$key]);
			}
		}

		return $removed_options;
	}

	// }}}
	// {{{ public function addOptionsByArray()

	/**
	 * Adds options to this option control using an associative array
	 *
	 * @param array $options an associative array of options. Keys are option
	 *                        values. Values are option titles.
	 * @param string $content_type optional. The content type of the option
	 *                              titles. If not specified, defaults to
	 *                              'text/plain'.
	 */
	public function addOptionsByArray(array $options,
		$content_type = 'text/plain')
	{
		foreach ($options as $value => $title)
			$this->addOption($value, $title, $content_type);
	}

	// }}}
	// {{{ protected function getOptions()

	/**
	 * Gets a reference to the array of options
	 *
	 * Subclasses may want to override this method.
	 *
	 * @return array a reference to the array of options.
	 */
	protected function &getOptions()
	{
		return $this->options;
	}

	// }}}
	// {{{ protected function getOption()

	/**
	 * Gets an option within this option control
	 *
	 * @param integer $index the ordinal position of the option within this
	 *                        option control.
	 *
	 * @return SwatOption a reference to the option, or null if no such option
	 *                     exists within this option control.
	 */
	protected function getOption($index)
	{
		$option = null;

		if (array_key_exists($index, $this->options))
			$option = $this->options[$index];

		return $option;
	}

	// }}}
}

?>
