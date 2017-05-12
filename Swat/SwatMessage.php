<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A data class to store a message
 *
 * SwatMessage objects are used thoughout Swat. The most noticeable
 * place they are used is for validating entry widgets. See also
 * {@link SwatMessageDisplay}, a control for displaying messages.
 *
 * A message has primary text, optional secondary text and a type. In order to
 * ease the creation of new messages types, message types are not strictly
 * defined. There are, however, several conventional message types. They are:
 *
 * - <strong>notice</strong>,
 * - <strong>warning</strong>,
 * - <strong>error</strong>,
 * - <strong>system-error</strong>, and
 * - <strong>cart</strong>.
 *
 * Messages do not have to use one of these types, but if they do, the message
 * will automatically be appropriately styled by Swat.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
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
	public function __construct($primary_content, $type = null)
	{
		if ($type == '') {
			$type = 'notice';
		}

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

		// legacy style for backwards compatibility
		if ($this->type === 'notice') {
			$classes[] = 'swat-message-notification';
		}

		// type-specific style
		if ($this->type != '') {
			$classes[] = 'swat-message-'.$this->type;
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
