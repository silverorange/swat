<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A control to display {@link SwatMessage} objects
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
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
	// {{{ public properties

	/**
	 * Text to display for closing the message
	 *
	 * By default, "Dismiss message."
	 *
	 * @var string
	 */
	public $close_text = null;

	// }}}
	// {{{ protected properties

	/**
	 * The messages to display
	 *
	 * This is an array of {@link SwatMessage} objects.
	 *
	 * @var array
	 */
	protected $display_messages = array();

	/**
	 * Messages in this display that are dismissable
	 *
	 * This is an array with values corresponding to a keys in the
	 * {@link SwatMessageDisplay::$display_messages} array.
	 *
	 * @var array
	 */
	protected $dismissable_messages = array();

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

		$this->addJavaScript(
			'packages/swat/javascript/swat-message-display.js'
		);

		$this->addStyleSheet('packages/swat/styles/swat-message.css');
		$this->addStyleSheet('packages/swat/styles/swat-message-display.css');
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

		$this->display_messages[] = $message;

		if ($dismissable == self::DISMISS_AUTO) {
			$dismissable = (in_array($message->type,
				$this->getDismissableMessageTypes())) ?
				self::DISMISS_ON : self::DISMISS_OFF;
		}

		if ($dismissable == self::DISMISS_ON)
			$this->dismissable_messages[] = count($this->display_messages) - 1;
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
		$wrapper_div->id = $this->id;
		$wrapper_div->class = $this->getCSSClassString();
		$wrapper_div->open();

		$has_dismiss_link = false;

		$message_count = count($this->display_messages);

		$count = 1;
		foreach ($this->display_messages as $key => $message) {
			if (in_array($key, $this->dismissable_messages)) {
				$has_dismiss_link = true;
			}

			$first = ($count === 1);
			$last  = ($count === $message_count);

			$this->displayMessage($key, $message, $first, $last);

			$count++;
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
		return count($this->display_messages);
	}

	// }}}
	// {{{ protected function displayMessage()

	/**
	 * Display a single message of this message display
	 *
	 * @param integer $message_id a unique identifier for the message within
	 *                             this message display.
	 * @param SwatMessage $message the message to display.
	 * @param boolean $first optional. Whether or not the message is the first
	 *                       message in this message display.
	 * @param boolean $last optional. Whether or not the message is the last
	 *                       message in this message display.
	 */
	protected function displayMessage($message_id, SwatMessage $message,
		$first = false, $last = false)
	{
		$message_div = new SwatHtmlTag('div');
		$container_div = new SwatHtmlTag('div');

		$message_div->id = $this->id.'_'.$message_id;
		$message_div->class = $message->getCSSClassString();

		if ($first) {
			$message_div->class.= ' swat-message-first';
		}

		if ($last) {
			$message_div->class.= ' swat-message-last';
		}

		$message_div->open();

		$container_div->class = 'swat-message-container';
		$container_div->open();

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
			'notice',
			'warning',
			'cart',
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

		$javascript.= sprintf("var %s_obj = new %s('%s', %s);",
			$this->id, $this->getJavaScriptClass(), $this->id,
			$dismissable_messages);

		return $javascript;
	}

	// }}}
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this message
	 * display
	 *
	 * Sub-classes of this class may want to return a sub-class of the default
	 * JavaScript form class.
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 form . Defaults to 'SwatMessageDisplay'.
	 */
	protected function getJavaScriptClass()
	{
		return 'SwatMessageDisplay';
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
		$close_text = ($this->close_text === null) ?
			Swat::_('Dismiss message.') : $this->close_text;

		return "SwatMessageDisplayMessage.close_text = '{$close_text}';\n";
	}

	// }}}
}

?>
