<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * A data class to store a message
 *
 * SwatMessage objects are used thoughout Swat. The most noticeable
 * place they are used is for validating entry widgets. See
 * also {@link SwatMessageDisplay}.
 *
 * @package   Swat
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessage extends SwatObject
{
	// {{{ constants

	/**
	 * Notification message type
	 *
	 * An informative message that doesn't require any action by the user.
	 *
	 * @deprecated Use the string 'notice' instead.
	 */
	const NOTIFICATION = 'notice';

	/**
	 * Warning message type
	 *
	 * A warning message that requires the attention of the user, but is
	 * not critical and does not necessarily require any action by the user.
	 *
	 * @deprecated Use the string 'warning' instead.
	 */
	const WARNING = 'warning';

	/**
	 * Error message type
	 *
	 * An error message that requires the attention of the user and that is
	 * expected/handled by the application.
	 * eg. Missing required fields
	 *
	 * @deprecated Use the string 'error' instead.
	 */
	const ERROR = 'error';

	/**
	 * System Error message type
	 *
	 * A system error that requires the attention of the user.
	 * eg. Database connection error
	 *
	 * @deprecated Use the string 'system-error' instead.
	 */
	const SYSTEM_ERROR = 'system-error';

	// }}}
	// {{{ public properties

	/**
	 * Type of message
	 *
	 * @var string
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

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new SwatMessage
	 *
	 * @param string $primary_content the primary text of the message.
	 * @param string $type optional. The type of message. If not specified,
	 *                      'notice' is used.
	 */
	public function __construct($primary_content, $type = self::NOTIFICATION)
	{
		$this->primary_content = $primary_content;
		$this->type = $type;
	}

	// }}}
	// {{{ public function getCSSClassString()

	/**
	 * Gets the CSS class names of this message as a string
	 *
	 * @return string the CSS class names of this message.
	 */
	public function getCSSClassString()
	{
		$classes = array('swat-message');

		// legacy styles
		switch ($this->type) {
		case SwatMessage::NOTIFICATION :
			$classes[] = 'swat-message-notification';
			break;
		case SwatMessage::WARNING :
			$classes[] = 'swat-message-warning';
			break;
		case SwatMessage::ERROR :
			$classes[] = 'swat-message-error';
			break;
		case SwatMessage::SYSTEM_ERROR :
			$classes[] = 'swat-message-system-error';
			break;
		}

		if ($this->type != '') {
			$classes[] = $this->type;
		}

		if ($this->secondary_content !== null) {
			$classes[] = 'swat-message-with-secondary';
		}

		return implode(' ', $classes);
	}

	// }}}
	// {{{ public function getCssClass()

	/**
	 * An alias for SwatMessage::getCSSClassString()
	 *
	 * @return string the CSS class names of this message.
	 *
	 * @deprecated Use SwatMessage::getCSSClassString() instead.
	 */
	public function getCssClass()
	{
		return $this->getCSSClassString();
	}

	// }}}
}

?>
