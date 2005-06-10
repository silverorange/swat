<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container with a decorative frame and optional title
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFrame extends SwatContainer
{

	/**
	 * A visible title for this frame, or null
	 * @var string
	 */
	public $title = null;

	/**
	 * CSS class to use on the HTML div where the error message is displayed.
	 * @var string
	 */
	public $errormsg_class = 'swat-frame-errormsg';

	public function display()
	{
		if (!$this->visible)
			return;

		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title !== null) {
			/*
			 * Experimental: Header level is autocalculated based on the 
			 * level of the frame in the widget tree.  Top level frame
			 * is currently an <h2>.
			 */
			$level = 2;
			$ancestor = $this->parent;

			while ($ancestor !== null) {
				if ($ancestor instanceof SwatFrame)
					$level++;

				$ancestor = $ancestor->parent;
			}

			echo "<h{$level}>{$this->title}</h{$level}>";
		}

		$inner_div->open();

		$this->displayErrorMessages();
		parent::display();

		$inner_div->close();
		$outer_div->close();
	}

	private function displayErrorMessages()
	{
		$messages = $this->gatherMessages(false);

		if (count($messages) > 0) {
			// TODO: more classes based on message type?
			$msg_div = new SwatHtmlTag('div');
			$msg_div->class = $this->errormsg_class;
			
			$msg_div->open();

			foreach ($messages as &$msg)
				echo $msg->content, '<br />';

			$msg_div->close();
		}
	}
}

?>
