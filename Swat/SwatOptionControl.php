<?php

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatOption.php';

/**
 * A base class for controls that need a set of options
 *
 * @package   Swat
 * @copyright 2006 silverorange
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

	// }}}
	// {{{ protected properties

	/**
	 * Are values unique?
	 *
	 * Whether or not values of options are required to be unique.
	 *
	 * @var boolean
	 */
	protected $unique_values = false;

	// }}}
	// {{{ public function addOption()

	/**
	 * Add an option element
	 *
	 * @param mixed $value Either a simply value for the option, or a
	 *                      {@link SwatOption} object. If a
	 *                      {@link SwatOption} object is used, the
	 *                      $title parameter of addOption will be ignored.
	 * @param string $title The title of the option element.
	 * @param string $content_type Optional content type of the title.
	 */
	public function addOption($value, $title = '', $content_type = 'text/plain')
	{
		if ($value instanceof SwatOption)
			$option = $value;
		else
			$option = new SwatOption($value, $title, $content_type);

		if ($this->unique_values)
			$this->options[$value] = $option;
		else
			$this->options[] = $option;
	}

	// }}}
	// {{{ public function addOptionsByArray()

	/**
	 * Add an option element
	 *
	 * @param array $options An associative array of options.
	 * @param string $content_type Optional content type of the titles.
	 */
	public function addOptionsByArray($options, $content_type = 'text/plain')
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
	 * Gets a reference to an option
	 *
	 * If $unique_values is true, index is the $value of the option.
	 * If $unique_values is false, index is the ordinal of the option.
	 *
	 * @return SwatOption a reference to the option, or null.
	 */
	protected function getOption($index)
	{
		if (array_key_exists($index, $this->options))
			return $this->options[$index];

		return null;
	}

	// }}}
}

?>
