<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/exceptions/SwatUndefinedMessageTypeException.php';

/**
 * A data class to store a message  
 *
 * SwatMessages are used thoughout Swat. The most noticable place they are used
 * is for validating entry widgets. See also {@link SwatMessageDisplay}.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessage extends SwatObject
{
	/**
	 * Notification message type
	 * 
	 * An informative message that doesn't require any action by the user.
	 */
	const NOTIFICATION = 1;

	/**
	 * Warning message type
	 * 
	 * A warning message that requires the attention of the user, but is
	 * not critical and does not necessarily require any action by the user.
	 */
	const WARNING = 2;

	/**
	 * Error message type
	 * 
	 * An error message that requires the attention of the user and that is
	 * expected/handled by the application.
	 * eg. Missing required fields
	 */
	const ERROR = 3;

	/**
	 * System Error message type
	 *
	 * A system error that requires the attention of the user.
	 * eg. Database connection error
	 */
	const SYSTEM_ERROR = 4;

	/**
	 * Type of message
	 *
	 * @var integer
	 */
	public $type;

	/**
	 * Primary message content
	 *
	 * The primary message content is a brief description. It should be about
	 * one sentence long.
	 *
	 * @var string
	 */
	public $primary_content = null;

	/**
	 * Secondary message text
	 *
	 * The secondary message content is an optional longer description. Its
	 * length should be at most the length of a small paragraph.
	 *
	 * @var string
	 */
	public $secondary_content = null;

	/**
	 * Optional content type for both primary and secondary content
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * Creates a new SwatMessage
	 *
	 * @param string $primary_content the primary text of the message.
	 * @param integer $type the type of message. Must be a valid class
	 *                       constant.
	 *
	 * @throws SwatUndefinedMessageTypeException
	 */
	public function __construct($primary_content, $type = self::NOTIFICATION)
	{
		$this->primary_content = $primary_content;

		$valid_types = array(
			self::NOTIFICATION,
			self::WARNING,
			self::ERROR,
			self::SYSTEM_ERROR);

		if ($type !== null) {
			if (in_array($type, $valid_types))
				$this->type = $type;
			else
				throw new SwatUndefinedMessageTypeException(
					"'{$type}' is not a valid SwatMessage message type.",
					0, $type);
		}
	}

	public function getCssClass()
	{
		$class = 'swat-message';

		switch ($this->type) {
			case SwatMessage::NOTIFICATION :
				$class.= ' swat-message-notification';
				break;
			case SwatMessage::WARNING :
				$class.= ' swat-message-warning';
				break;
			case SwatMessage::ERROR :
				$class.= ' swat-message-error';
				break;
			case SwatMessage::SYSTEM_ERROR :
				$class.= ' swat-message-system-error';
				break;
		}

		return $class;
	}
}

?>
