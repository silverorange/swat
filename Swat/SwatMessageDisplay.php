<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/SwatYUI.php';

/**
 * A control to display {@link SwatMessage} objects
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageDisplay extends SwatControl
{
	// {{{ class constants

	/**
	 * Dismiss link for message is on.
	 */
	const DISMISS_ON   = 1;

	/**
	 * Dismiss link for message is off.
	 */
	const DISMISS_OFF  = 2;

	/**
	 * Dismiss link for message is automatically displayed for certain message
	 * types.
	 *
	 * @see SwatMessageDisplay::getDismissableMessageTypes()
	 */
	const DISMISS_AUTO = 3;

	// }}}
	// {{{ private properties

	/**
	 * The messages to display
	 *
	 * This is an array of {@link SwatMessage} objects.
	 *
	 * @var array
	 */
	private $_messages = array();

	/**
	 * Messages in this display that are dismissable
	 *
	 * This is an array with values corresponding to a keys in the
	 * {@link SwatMessageDisplay::$_messages} array.
	 *
	 * @var array
	 */
	private $dismissable_messages = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new message display
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/swat/javascript/swat-message-display.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-message.css',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-message-display.css',
			Swat::PACKAGE_ID);

		$this->addTangoAttribution();
	}

	// }}}
	// {{{ public function add()

	/**
	 * Adds a message
	 *
	 * Adds a new message. The message will be shown by the display() method
	 *
	 * @param string|SwatMessage $message the message to add. If the message is
	 *                                     a string, a new {@link SwatMessage}
	 *                                     object of the default message type
	 *                                     is created.
	 * @param integer $dismissable optional. Whether or not to show a dismiss
	 *                              link for the added message on this message
	 *                              display. This should be one of the
	 *                              SwatMessageDisplay::DISMISS_* constants.
	 *                              By default, the <i>$dismiss_link</i> is
	 *                              set to
	 *                              {@link SwatMessageDisplay::DISMISS_AUTO}.
	 *
	 * @throws SwatInvalidClassException
	 */
	public function add($message, $dismissable = self::DISMISS_AUTO)
	{
		if (is_string($message)) {
			$message = new SwatMessage($message);
		} elseif (!($message instanceof SwatMessage)) {
			throw new SwatInvalidClassException(
				'Cannot add message. $message must be either a string or a '.
				'SwatMessage.', 0, $message);
		}

		$this->_messages[] = $message;

		if ($dismissable == self::DISMISS_AUTO) {
			$dismissable = (in_array($message->type,
				$this->getDismissableMessageTypes())) ?
				self::DISMISS_ON : self::DISMISS_OFF;
		}

		if ($dismissable == self::DISMISS_ON)
			$this->dismissable_messages[] = count($this->_messages) - 1;
	}

	// }}}
	// {{{ public function get()

	/**
	 * Get a message in this message display
	 *
	 * @return SwatMessage the message.
	 */
	public function get($index = 0)
	{
		if ($index < $this->getMessageCount())
			$message = $this->_messages[$index];
		else
			$message = null;

		return $message;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays messages in this message display
	 *
	 * The CSS class of each message is determined by the message being
	 * displayed.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->getMessageCount() == 0)
			return;

		parent::display();

		$wrapper_div = new SwatHtmlTag('div');
		$message_div = new SwatHtmlTag('div');
		$container_div = new SwatHtmlTag('div');

		$wrapper_div->id = $this->id;
		$wrapper_div->class = $this->getCSSClassString();
		$wrapper_div->open();

		$has_dismiss_link = false;

		foreach ($this->_messages as $key => $message) {
			$message_div->id = $this->id.'_'.$key;
			$message_div->class = $message->getCSSClassString();
			$message_div->open();

			$container_div->class = 'swat-message-container';
			$container_div->open();

			if (in_array($key, $this->dismissable_messages))
				$has_dismiss_link = true;

			$primary_content = new SwatHtmlTag('h3');
			$primary_content->class = 'swat-message-primary-content';
			$primary_content->setContent(
				$message->primary_content, $message->content_type);

			$primary_content->display();

			if ($message->secondary_content !== null) {
				$secondary_div = new SwatHtmlTag('div');
				$secondary_div->class = 'swat-message-secondary-content';
				$secondary_div->setContent(
					$message->secondary_content, $message->content_type);

				$secondary_div->display();
			}

			$container_div->close();
			$message_div->close();
		}

		$wrapper_div->close();

		if ($has_dismiss_link)
			Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function isVisible()

	/**
	 * Gets whether or not this message display is visible
	 *
	 * The message display control respectes normal visibility rules. In
	 * addition, a message display is considered not visible if it contains no
	 * messages.
	 *
	 * @return boolean true if this message display is visible and false if it
	 *                  is not.
	 *
	 * @see SwatUIObject::isVisible()
	 */
	public function isVisible()
	{
		return (($this->getMessageCount() > 0) && parent::isVisible());
	}

	// }}}
	// {{{ public function getMessageCount()

	/**
	 * Gets the number of messages in this message display
	 *
	 * @return integer the number of messages in this message display.
	 */
	public function getMessageCount()
	{
		return count($this->_messages);
	}

	// }}}
	// {{{ protected function getDismissableMessageTypes()

	/**
	 * Gets an array of message types that are dismissable by default
	 *
	 * @return array message types that are dismissable by default.
	 */
	protected function getDismissableMessageTypes()
	{
		return array(
			SwatMessage::NOTIFICATION,
			SwatMessage::WARNING,
		);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this message display
	 *
	 * @return array the array of CSS classes that are applied to this message
	 *                display.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-message-display');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for hiding messages
	 */
	protected function getInlineJavaScript()
	{
		static $shown = false;

		if (!$shown) {
			$javascript = $this->getInlineJavaScriptTranslations();
			$shown = true;
		} else {
			$javascript = '';
		}

		$dismissable_messages =
			'['.implode(', ', $this->dismissable_messages).']';

		$javascript.= sprintf("var %s_obj = new SwatMessageDisplay('%s', %s);",
			$this->id, $this->id, $dismissable_messages);

		return $javascript;
	}

	// }}}
	// {{{ protected function getInlineJavaScriptTranslations()

	/**
	 * Gets translatable string resources for the JavaScript object for
	 * this widget
	 *
	 * @return string translatable JavaScript string resources for this widget.
	 */
	protected function getInlineJavaScriptTranslations()
	{
		$close_text  = Swat::_('Dismiss message.');
		return "SwatMessageDisplayMessage.close_text = '{$close_text}';\n";
	}

	// }}}
}

?>
