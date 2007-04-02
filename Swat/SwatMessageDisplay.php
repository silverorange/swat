<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/SwatYUI.php';

/**
 * A control to display page status messages
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageDisplay extends SwatControl
{
	// {{{ private properties

	/**
	 * The messages to display
	 *
	 * The messages are stored as an array of references to SwatMessage
	 * objects.
	 *
	 * @var array
	 *
	 * @see SwatMessage
	 */
	private $_messages = array();

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
	 * @param mixed $message either a {@link SwatMessage} object or a string to
	 *                    add to this display.
	 *
	 * @throws SwatInvalidClassException
	 */
	public function add($message)
	{
		if (is_string($message)) {
			$this->_messages[] = new SwatMessage($message);
		} elseif ($message instanceof SwatMessage) {
			$this->_messages[] = $message;
		} else {
			throw new SwatInvalidClassException(
				'Cannot add message. Message must be either a string or a '.
				'SwatMessage.', 0, $message);
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays the messages
	 *
	 * The CSS class of each message is determined by the message being
	 * displayed.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if (count($this->_messages) == 0)
			return;

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

			if ($message->type == SwatMessage::NOTIFICATION ||
				$message->type == SwatMessage::WARNING) {
				$has_dismiss_link = true;
			}

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
		$classes = array_merge($classes, $this->classes);
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

		$hideable_messages = array();

		foreach ($this->_messages as $key => $message) {
			switch ($message->type) {
			case SwatMessage::NOTIFICATION:
			case SwatMessage::WARNING:
				$hideable_messages[] = $key;
				break;
			}
		}

		$hideable_messages = '['.implode(', ', $hideable_messages).']';

		$javascript.= sprintf("var %s_obj = new SwatMessageDisplay('%s', %s);",
			$this->id, $this->id, $hideable_messages);

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
		$close_text  = Swat::_('Dismiss message');
		return "SwatMessageDisplay.close_text = '{$close_text}';\n";
	}

	// }}}
}

?>
