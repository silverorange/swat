<?php

/**
 * A data class to store a message  
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessage
{

	/**
	 * An informative message that doesn't require any action by the user.
	 */
	const INFO = 1;

	/**
	 * An warning message that requires the attention of the user, but is
	 * not critical and does not necessarily require any action by the user.
	 */
	const WARNING = 2;
	const USER_ERROR = 3;
	const ERROR = 4;

	/**
	 * Type of message
	 *
	 * @var integer
	 */
	public $type;

	/**
	 * Message text
	 *
	 * @var string
	 */
	public $content = null;

	/**
	 * Creates a new SwatMessage
	 *
	 * @param string $content the text of the message.
	 * @param integer $type the type of message. Must be a valid class
	 *                       constant.
	 */
	public function __construct($content, $type = self::INFO)
	{
		$this->content = $content;
		if ($type !== null)
			$this->type = $type;
	}
}

?>
