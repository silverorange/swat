<?php

require_once 'Swat/SwatObject.php';

/**
 * A class representing a divider in a flydown
 *
 * This class is for semantic purposed only. The flydown handles all the
 * displaying of dividers and regular flydown options.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFlydownDivider extends SwatFlydownOption
{
	/**
	 * Creates a flydown option
	 *
	 * @param mixed $value value of the option.
	 * @param string $title displayed title of the divider. This defaults to
	 *                       two em dashes.
	 */
	public function __construct($value, $title = '&#8212;&#8212;')
	{
		$this->value = $value;
		$this->title = $title;
	}
}

?>
