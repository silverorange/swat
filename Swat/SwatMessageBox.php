<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatMessage.php');

/**
 * A control to display page status messages  
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatMessageBox extends SwatControl {

	/**
	 * A visible name for this frame, or null
	 * @var string
	 */
	public $title = null;

	/**
	 * Array of {@link SwatMessage}s
	 * @var array
	 */
	public $messages = array();

	public function display() {

		if ($this->title === null && count($this->messages) == 0)
			return;

		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title !== null) {
			echo "<h2>{$this->title}</h2>";
		}

		$inner_div->open();
		
		$message_div = new SwatHtmlTag('div');
		foreach ($this->messages as $message) {
			switch ($message->type) {
				case SwatMessage::INFO :
					$message_div->class = 'swat-message-info';
					break;
				case SwatMessage::WARNING :
					$message_div->class = 'swat-message-warning';
					break;
				case SwatMessage::ERROR :
					$message_div->class = 'swat-message-error';
					break;
			}
			$message_div->open();
			echo $message->content;
			$message_div->close();
		}

		$inner_div->close();
		$outer_div->close();
	}
}

?>
