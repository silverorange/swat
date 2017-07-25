<?php

/**
 * A base class for controls using a set of options
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
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
	 * Metadata for the options of this control
	 *
	 * An array with the object hash of the option as the key and a sub-array
	 * of name-value pairs as the metadata. For example:
	 *
	 * <code>
	 * <?php
	 * array(
	 *     spl_object_hash($option1) => array(
	 *         'classes' => array('small'),
	 *     ),
	 *     spl_object_hash($option2) => array(
	 *         'classes' => array('large'),
	 *     ),
	 * );
	 * ?>
	 * </code>
	 *
	 * Any metadata may be added to options. It is up to the control to make
	 * use of particular metadata fields. Common metadata fields are:
	 *
	 * - classes - an array of CSS classes
	 *
	 * @var array
	 *
	 * @see SwatOptionControl::addOption()
	 * @see SwatOptionControl::addOptionMetadata()
	 * @see SwatOptionControl::getOptionMetadata()
	 */
	protected $option_metadata = array();

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
	 * This method has a number of signatures. You can add an existing option
	 * object, or create a new option object from a value and title:
	 *
	 * <code>
	 * <?php
	 * // 1. add a new option from value and title
	 * $control->addOption(123, 'Option Title');
	 *
	 * // 2. add an existing option object
	 * $option = new SwatOption(123, 'Option Title');
	 * $control->addOption($option);
	 *
	 * // 3. add an option with metadata
	 * $option = new SwatOption(123, 'Option Title');
	 * $control->addOption($option, array('classes' => array('large')));
	 * ?>
	 * </code>
	 *
	 * @param mixed|SwatOption $value either a value for the option, or a
	 *                                 {@link SwatOption} object. If a
	 *                                 SwatOption is used, the
	 *                                 <i>$content_type</i> parameter of
	 *                                 this method call is ignored and the
	 *                                 <i>$title</i> parameter may be used to
	 *                                 specify option metadata.
	 * @param array|string $title optional. Either a string containing the
	 *                             title of the added option, or an array
	 *                             containing metadata for the SwatOption
	 *                             specified in the <i>$value</i> parameter.
	 * @param string $content_type optional. The content type of the title. If
	 *                              not specified, defaults to 'text/plain'.
	 *                              Ignored if the <i>$value</i> parameter is
	 *                              a SwatOption object.
	 *
	 * @see SwatOptionControl::$options
	 * @see SwatOptionControl::addOptionMetadata()
	 */
	public function addOption($value, $title = '', $content_type = 'text/plain')
	{
		if ($value instanceof SwatOption) {
			$option = $value;
		} else {
			$option = new SwatOption($value, $title, $content_type);
		}

		$this->options[] = $option;

		// initialize metadata
		$key = $this->getOptionMetadataKey($option);
		if (!isset($this->option_metadata[$key])) {
			// use isset so we don't erase the metadata if an option is added
			// twice
			$this->option_metadata[$key] = array();
		}

		if ($value instanceof SwatOption && is_array($title)) {
			$this->addOptionMetadata($option, $title);
		} else {
			$this->addOptionMetadata($option, array());
		}
	}

	// }}}
	// {{{ public function addOptionMetadata()

	/**
	 * Sets the metadata for an option
	 *
	 * Any metadata may be added to options. It is up to the control to make
	 * use of particular metadata fields. Common metadata fields are:
	 *
	 * - classes - an array of CSS classes
	 *
	 * @param SwatOption $option the option for which to set the metadata.
	 * @param array|string $metadata either an array of metadata to add to the
	 *                                option, or a string specifying the name
	 *                                of the metadata field to add.
	 * @param mixed $value optional. If the <i>$metadata</i> parameter is a
	 *                      string, this is the metadata value to set for the
	 *                      option. Otherwise, this parameter is ignored.
	 *
	 * @see SwatOptionControl::addOption()
	 * @see SwatOptionControl::getOptionMetadata()
	 */
	public function addOptionMetadata(
		SwatOption $option,
		$metadata,
		$value = null
	) {
		$key = $this->getOptionMetadataKey($option);

		if (is_array($metadata)) {
			$this->option_metadata[$key] = array_merge(
				$this->option_metadata[$key], $metadata);
		} else {
			$this->option_metadata[$key][$metadata] = $value;
		}
	}

	// }}}
	// {{{ public function getOptionMetadata()

	/**
	 * Gets the metadata for an option
	 *
	 * Any metadata may be added to options. It is up to the control to make
	 * use of particular metadata fields. Common metadata fields are:
	 *
	 * - classes - an array of CSS classes
	 *
	 * @param SwatOption $option the option for which to get the metadata.
	 * @param string $metadata optional. An optional metadata property to get.
	 *                          If not specified, all available metadata for
	 *                          the option is returned.
	 *
	 * @returns array|mixed an array of the metadata for this option, or a
	 *                      specific metadata value if the <i>$metadata</i>
	 *                      parameter is specified. If <i>$metadata</i> is
	 *                      specified and no such metadata field exists for the
	 *                      specified option, null is returned.
	 *
	 * @see SwatOptionControl::addOptionMetadata()
	 */
	public function getOptionMetadata(SwatOption $option, $metadata = null)
	{
		$key = $this->getOptionMetadataKey($option);

		if ($metadata === null) {
			if (isset($this->option_metadata[$key])) {
				$metadata = $this->option_metadata[$key];
			} else {
				$metadata = array();
			}
		} else {
			if (isset($this->option_metadata[$key]) &&
				isset($this->option_metadata[$key][$metadata])) {
				$metadata = $this->option_metadata[$key][$metadata];
			} else {
				$metadata = null;
			}
		}

		return $metadata;
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

				// remove from options list
				unset($this->options[$key]);

				// remove metadata
				$key = $this->getOptionMetadataKey($control_option);
				unset($this->option_metadata[$key]);
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

				// remove from options list
				unset($this->options[$key]);

				// remove metadata
				$metadata_key = $this->getOptionMetadataKey($control_option);
				unset($this->option_metadata[$metadata_key]);
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
	public function addOptionsByArray(
		array $options,
		$content_type = 'text/plain'
	) {
		foreach ($options as $value => $title)
			$this->addOption($value, $title, $content_type);
	}

	// }}}
	// {{{ public function getOptionsByValue()

	/**
	 * Gets options from this option control by their value
	 *
	 * @param mixed $value the value of the option or options to get.
	 *
	 * @return array an array of SwatOption objects or an empty array if no
	 *                options with the given value exist within this option
	 *                control.
	 */
	public function getOptionsByValue($value)
	{
		$options = array();

		foreach ($this->options as $option)
			if ($option->value === $value)
				$options[] = $option;

		return $options;
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
	// {{{ protected function getOptionMetadataKey()

	/**
	 * Gets the key used to load and store metadata for an option
	 *
	 * @param SwatOption $option the option for which to get the key.
	 *
	 * @return string the key used to load and store metadata for the specified
	 *                option.
	 */
	protected function getOptionMetadataKey(SwatOption $option)
	{
		return spl_object_hash($option);
	}

	// }}}
}

?>
