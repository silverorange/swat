<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A control to display page status messages  
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMessageDisplay extends SwatControl
{
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
 
	/**
	 * Show Dismiss link
	 * 
	 * Toggles a Dismiss link in the SwatMessageDisplay box
	 * that hides the box. Off by default.
	 *
	 * @var boolean
	 */
	public $show_dismiss_link = false;

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

		$this->addJavaScript('swat/javascript/swat-message-display.js');
		$this->addStyleSheet('swat/styles/swat-message-display.css');
	}

	/**
	 * Adds a message
	 *
	 * Adds a new message. The message will be shown by the display() method
	 *
	 * @param mixed $msg either a {@link SwatMessage} object or a string to
	 *                    add to this display.
	 *
	 * @throws SwatInvalidClassException
	 */
	public function add($msg)
	{
		if (is_string($msg)) {
			$this->_messages[] = new SwatMessage($msg);
		} elseif ($msg instanceof SwatMessage) {
			$this->_messages[] = $msg;
		} else {
			throw new SwatInvalidClassException(
				'Cannot add message. Message must be either a string or a '.
				'SwatMessage.', 0, $msg);
		}
	}

	/**
	 * Displays the messages
	 *
	 * The CSS class of each message is determined by the type of message being
	 * displayed.
	 */
	public function display()
	{
		$has_dismiss_link = false;

		if (!$this->visible)
			return;

		if (count($this->_messages) == 0)
			return;

		$div = new SwatHtmlTag('div');

		foreach ($this->_messages as $key => $message) {
			$div->id = $this->id.'_'.$key;

			switch ($message->type) {
				case SwatMessage::NOTIFICATION :
					$div->class = 'swat-message-display-notification';
					break;
				case SwatMessage::WARNING :
					$div->class = 'swat-message-display-warning';
					break;
				case SwatMessage::ERROR :
					$div->class = 'swat-message-display-error';
					break;
				case SwatMessage::SYSTEM_ERROR :
					$div->class = 'swat-message-display-system-error';
					break;
			}

			if ($message->secondary_content !== null) 
				$div->class .= ' swat-message-display-with-secondary';

			$div->open();

			if ($this->show_dismiss_link) {
				$dismiss_link = new SwatHtmlTag('a');
				$dismiss_link->href =
					"javascript:{$this->id}_obj.hideMessage({$key})";

				$dismiss_link->class = 'swat-message-display-dismiss-link';
				$dismiss_link->title = _('Dismiss this Message');
				$dismiss_link->content = _('Dismiss this Message');
				$dismiss_link->display();
				$has_dismiss_link = true;
			}

			$primary_content = new SwatHtmlTag('h3');
			$primary_content->class = 'swat-message-display-primary-content';
			$primary_content->content = $message->primary_content;
			$primary_content->display();

			if ($message->secondary_content !== null) {
				$secondary_div = new SwatHtmlTag('div');
				$secondary_div->class = 'swat-message-display-secondary-content';
				$secondary_div->content = $message->secondary_content;
				$secondary_div->display();
			}

			$div->close();

		}

		if ($has_dismiss_link)
			$this->displayJavaScript();
	}

	/**
	 * Displays the JavaScript for hiding messages
	 */
	private function displayJavaScript()
	{
		echo '<script type="text/javascript">'."\n";
		printf("%s_obj = new SwatMessageDisplay('%s', %s);\n",
			$this->id, $this->id, count($this->_messages));

		echo '</script>';
	}
}

?>
