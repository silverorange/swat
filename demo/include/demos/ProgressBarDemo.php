<?php

require_once 'Demo.php';

/**
 * A demo using progress bars
 *
 * This demo sets up the message explaining the first JavaScript enabled
 * progress bar.
 *
 * @package   SwatDemo
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ProgressBarDemo extends Demo
{
	// {{{ public function buildDemoUI()

	public function buildDemoUI(SwatUI $ui)
	{
		$message = new SwatMessage(
			'Progress bars are easily controlled from JavaScript.');

		$message->secondary_content =
			'Mouse over the Download Progress bar or click the Upload '.
			'Progress bar for a demonstration.';

		$ui->getWidget('note')->add($message, SwatMessageDisplay::DISMISS_OFF);
	}

	// }}}
}

?>
