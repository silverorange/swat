<?php

/**
 * A radio list selection widget for a Yes/No option.
 *
 * @package   Swat
 * @copyright 2009-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatYesNoRadioList extends SwatRadioList
{

	const NO  = false;
	const YES = true;

	/**
	 * Creates a new yes/no radio list
	 *
	 * Sets the options of this radio list to be yes and no.
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

}

?>
