<?php

require_once 'Swat/SwatObject.php';

/**
 * A data class to store a message  
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessage extends SwatObject
{

	/**
	 * Information message type
	 * 
	 * An informative message that doesn't require any action by the user.
	 */
	const INFO = 1;

	/**
	 * Warning message type
	 * 
	 * A warning message that requires the attention of the user, but is
	 * not critical and does not necessarily require any action by the user.
	 */
	const WARNING = 2;

	/**
	 * User Error message type
	 * 
	 * An error message that requires the attention of the user and that is
	 * expected/handled by the application.
	 * eg. Missing required fields
	 */
	const USER_ERROR = 3;

	/**
	 * Error message type
	 *
	 * A system error that requires the attention of the user.
	 * eg. Database connection error
	 */
	const ERROR = 4;

	/**
	 * Type of message
	 *
	 * @var integer
	 */
	public $type;

	/**
	 * Primary message text
	 *
	 * @var string
	 */
	public $primary_content = null;

	/**
	 * Secondary message text
	 *
	 * @var string
	 */
	public $secondary_content = null;

	/**
	 * Creates a new SwatMessage
	 *
	 * @param string $primary_content the primary text of the message.
	 * @param integer $type the type of message. Must be a valid class
	 *                       constant.
	 * @param string $secondary_content the secondary text of the message.
	 */
	public function __construct($primary_content, $type = self::INFO, $secondary_content = null)
	{
		$this->primary_content = $primary_content;

		if ($type !== null)
			$this->type = $type;

		$this->secondary_content = $secondary_content;
	}
}

?>
