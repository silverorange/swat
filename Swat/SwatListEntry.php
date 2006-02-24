<?php

require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatString.php';

/**
 * An input control for entering a delimited list of data
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatListEntry extends SwatEntry
{
	/**
	 * The values of this list entry
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * The delimiter for entries in this list entry
	 *
	 * This may be a single character or a string of characters. The delimiter
	 * is used to separate list entries. Entries in the
	 * {@link SwatListEntry::$values} array do not include the delimiter.
	 *
	 * By default, the delimiter is a comma.
	 *
	 * @var string
	 */
	public $delimiter = ',';

	/**
	 * Whether or not to trim whitespace from values between delimiters
	 *
	 * If this is true, whitespace before or after the delimiter is removed
	 * from entries in the array of values. If this is false, whitespace is
	 * maintained.
	 *
	 * For example, if the user enters 'orange, apple' in form
	 * data and {SwatListEntry::$trim_whitespace} is true, the second entry in
	 * {@link SwatListEntry::$values} array will be 'apple'. In the same
	 * example if {@link SwatListEntry::$trim_whitespace} is false, the second
	 * entry in the array will be ' apple'.
	 *
	 * @var boolean
	 */
	public $trim_whitespace = true;

	/**
	 * The maximum number of allowed entries in this list entry
	 *
	 * If this value is set to null or 0 then there is no maximum number of
	 * allowed entries.
	 *
	 * @var integer
	 */
	public $max_entries = null;

	/**
	 * The minimum number of required entries in this list entry
	 *
	 * If {@link SwatInputControl::$required} is set to true for this list
	 * entry this property specifies the minimum number of entries the user
	 * must enter.
	 *
	 * Note: If {@link SwatInputControl::$required} is set to false, this
	 * property has no effect. 
	 *
	 * @var integer
	 * @see SwatInputControl::$required
	 */
	public $min_entries = 1;

	/**
	 * Creates a new list entry widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->minlength = 1;
	}

	/**
	 * Displays this list entry
	 */
	public function display()
	{
		// Do not have a maxlength on the XHTML input tag. This relies on
		// internal knowledge of the parent::display() method.
		$old_maxlength = $this->maxlength;
		$this->maxlength = null;

		parent::display();

		$this->maxlength = $old_maxlength;
	}

	/**
	 * Processes this list entry widget
	 *
	 * The user entered values are split into an array of values and stored in
	 * the {@link SwatListEntry::$values} array.
	 */
	public function process()
	{
		$data = &$this->getForm()->getFormData();

		if (!isset($data[$this->id])) {
			$this->value = null;
		} elseif (strlen($data[$this->id]) == 0) {
			$this->value = null;
		} else {
			$this->value = $data[$this->id];
		}

		$this->values = $this->splitValues($this->value);

		if (!$this->required && count($this->values) == 0) {
			return;

		} elseif ($this->max_entries > 0 &&
			count($this->values) > $this->max_entries) {

			$msg = sprintf(
				Swat::_('The %%s field cannot have more than %s entries.'),
				SwatString::numberFormat($this->max_entries));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif ($this->required &&
			count($this->values) < $this->min_entries) {

			$msg = sprintf(Swat::ngettext(
				'The %%s field must have at least %s entry.',
				'The %%s field must have at least %s entries.',
				$this->min_entries),
				SwatString::numberFormat($this->min_entries));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}

		// validate individual values

		$min_length_msg = null;
		$max_length_msg = null;
		$min_length_error_values = array();
		$max_length_error_values = array();

		foreach ($this->values as $value) {
			$len = strlen($value);
			if ($this->maxlength !== null && $len > $this->maxlength) {
				$max_length_msg = sprintf(Swat::ngettext(
					'Entries in the %%s field must be less than %s '.
					'character long.',
					'Entries in the %%s field must be less than %s '.
					'characters long.',
					$this->maxlength),
					SwatString::numberFormat($this->maxlength)).' ';

				$max_length_error_values[] = $value;

			} elseif ($this->minlength !== null && $len < $this->minlength) {
				$min_length_msg = sprintf(Swat::ngettext(
					'Entries in the %%s field must be at least %s '.
					'character long.',
					'Entries in the %%s field must be at least %s '.
					'characters long.',
					$this->minlength),
					SwatString::numberFormat($this->minlength)).' ';

				$min_length_error_values[] = $value;
			}
		}

		if ($min_length_msg !== null) {
			$min_length_msg.= sprintf(Swat::ngettext(
				'The following entry is too short: %s.',
				'The following entries are too short: %s.',
				count($min_length_error_values)),
				implode(', ', $min_length_error_values),
				SwatString::numberFormat(count($min_length_error_values)));

			$this->addMessage(new SwatMessage($min_length_msg,
				SwatMessage::ERROR));
		}

		if ($max_length_msg !== null) {
			$max_length_msg.= sprintf(Swat::ngettext(
				'The following entry is too long: %s.',
				'The following entries are too long: %s.',
				count($max_length_error_values)),
				implode(', ', $max_length_error_values),
				SwatString::numberFormat(count($max_length_error_values)));

			$this->addMessage(new SwatMessage($max_length_msg,
				SwatMessage::ERROR));
		}
	}

	/**
	 * Gets the current state of this entry widget
	 *
	 * @return string the current state of this entry widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->values;
	}

	/**
	 * Sets the current state of this list entry widget
	 *
	 * @param string $state the new state of this list entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($values)
	{
		if (is_array($values))
			$this->values = $value;
		else
			$this->values = $this->splitValues($value);
	}

	/**
	 * Gets the value displayed in the XHTML input
	 *
	 * For list entry, this is a delimiter separated string containing the
	 * elements of {@link SwatListEntry::$values}.
	 *
	 * @return string the values displayed in the XHTML input.
	 */
	protected function getDisplayValue()
	{
		if ($this->trim_whitespace)
			return implode($this->delimiter.' ', $this->values);
		else
			return implode($this->delimiter, $this->values);
	}

	/**
	 * Gets a note describing the rules on this list entry
	 *
	 * This note informs the user what numbers of entries are valid for this
	 * list entry. This note does not mention anything about what type of
	 * delimiter is used. Developers are responsible for ensuring that users
	 * are notified what type of delimiters are used.
	 *
	 * @return string an informative note of how to use this list entry.
	 *
	 * @see SwatControl::getNote()
	 */
	public function getNote()
	{
		$note = null;

		if ($this->max_entries !== null && $this->max_entries > 0 &&
			$this->min_entries === null) {

			$note = sprintf(Swat::ngettext(
				'List can contain at most %s entry',
				'List can contain at most %s entries',
				$this->max_entries),
				SwatString::numberFormat($this->max_entries));

		} elseif (($this->max_entries === null  ||
			$this->max_entries == 0) &&
			$this->min_entries !== null && $this->required == true) {

			$note = sprintf(Swat::ngettext(
				'List must contain at least %s entry',
				'List must contain at least %s entries',
				$this->min_entries),
				SwatString::numberFormat($this->min_entries));

		} elseif ($this->max_entries !== null && $this->max_entries > 0 &&
			$this->min_entries !== null && $this->required == true) {

			$note = sprintf(
				'List must contain between %s and %s entries.',
				SwatString::numberFormat($this->min_entries),
				SwatString::numberFormat($this->max_entries));
		}

		return $note;
	}

	/**
	 * Splits a value string with entries separated by delimiters into
	 * an array
	 *
	 * If {@link SwatListEntry::$trim_whitespace} is set to true, whitespace
	 * is not included in the split values.
	 *
	 * @param string $value the string to split.
	 *
	 * @return array the string of delimiter separated values split into an
	 *                array of values.
	 */
	private function splitValues($value)
	{
		if ($this->trim_whitespace)
			return preg_split('/['.$this->delimiter.'\s]/u', $value, -1,
				PREG_SPLIT_NO_EMPTY);

		return preg_split('/'.$this->delimiter.'/u', $value, -1,
			PREG_SPLIT_NO_EMPTY);
	}
}

?>
