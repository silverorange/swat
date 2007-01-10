<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatString.php';

/**
 * Base class for numeric entry widgets
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatNumericEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * Show Thousands Seperator
	 *
	 * Whether or not to show a thousands separator (shown depending on
	 * locale). 
	 *
	 * @var boolean
	 */
	public $show_thousands_separator = true;

	/**
	 * The smallest valid number in this entry
	 *
	 * This is inclusive. If set to null, there is no minimum value.
	 *
	 * @var double
	 */
	public $minimum_value = null;

	/**
	 * The largest valid number in this entry
	 *
	 * This is inclusive. If set to null, there is no maximum value.
	 *
	 * @var double
	 */
	public $maximum_value = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new numeric entry widget
	 *
	 * Sets the input size to 10 by default.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 10;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Checks the minimum and maximum values of this numeric entry widget
	 */
	public function process()
	{
		parent::process();

		$value = $this->getNumericValue();
		if ($value !== null) {
			if ($this->minimum_value !== null &&
				$value < $this->minimum_value) {
				$message = $this->getValidationMessage('below-minimum');
				$message->primary_content = sprintf($message->primary_content, 
					SwatString::numberFormat($this->minimum_value));

				$this->addMessage($message);
			}

			if ($this->maximum_value !== null &&
				$value > $this->maximum_value) {
				$message = $this->getValidationMessage('above-maximum');
				$message->primary_content = sprintf($message->primary_content, 
					SwatString::numberFormat($this->maximum_value));

				$this->addMessage($message);
			}
		}
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this numeric entry
	 *
	 * @see SwatEntry::getValidationMessage()
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'below-minimum':
			$message = new SwatMessage(
				Swat::_('The %%s field must not be less than %s.'),
				SwatMessage::ERROR);

			break;

		case 'above-maximum':
			$message = new SwatMessage(
				Swat::_('The %%s field must not be more than %s.'),
				SwatMessage::ERROR);

			break;

		default:
			$message = parent::getValidationMessage($id);
			break;
		}

		return $message;
	}

	// }}}
	// {{{ abstract protected function getNumericValue()
	
	/**
	 * Gets the numeric value of this widget
	 *
	 * This allows each widget to parse raw values how they want to get numeric
	 * values.
	 *
	 * @return mixed the numeric value of this entry widget of null if no
	 *                numeric value is available.
	 */
	abstract protected function getNumericValue();

	// }}}
}

?>
