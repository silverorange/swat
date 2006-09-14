<?php

require_once 'Swat/SwatFlydown.php';

/**
 * A flydown (aka combo-box) selection widget for a Yes/No option.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatYesNoFlydown extends SwatFlydown
{
	// {{{ constants

	const NO = 1;
	const YES = 2;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new yes/no flydown
	 *
	 * Sets the options of this flydown to be yes and no.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addOptionsByArray(array(self::NO => Swat::_('No'),
			self::YES => Swat::_('Yes')));
	}

	// }}}
	// {{{ public function getValuesAsBoolean()

	/**
	 * Gets the value of this yes/no flydown as a boolean
	 *
	 * If the value is not set, this methods returns null.
	 *
	 * @return boolean the value of this yes/no flywdown.
	 */
	public function getValueAsBoolean()
	{
		switch ($this->value) {
		case self::NO:
			return false;
		case self::YES:
			return true;
		default:
			return null;
		}
	}

	// }}}
}

?>
