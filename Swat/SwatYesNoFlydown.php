<?php

/**
 * A flydown (aka combo-box) selection widget for a Yes/No option.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatYesNoFlydown extends SwatFlydown
{
	// {{{ constants

	const NO  = false;
	const YES = true;

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
		$this->addOption(self::NO, Swat::_('No'));
		$this->addOption(self::YES, Swat::_('Yes'));
	}

	// }}}
}

?>
