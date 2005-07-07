<?php

require_once 'Swat/SwatObject.php';

/**
 * A simple class for storing {@link SwatFlydown} options
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFlydownOption extends SwatObject
{
	/**
	 * Option title
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Option value
	 *
	 * @var mixed
	 */
	public $value = null;

	/**
	 * Creates a flydown option
	 *
	 * @param mixed $value Value of the option.
	 * @param string $title Displayed title of the option.
	 */
	public function __construct($value, $title)
	{
		$this->value = $value;
		$this->title = $title;
	}
}

?>
