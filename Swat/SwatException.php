<?php


/**
 * A Swat Exception.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatException extends Exception {

	public function process() {

		if (ini_get('display_errors')) {
			$this->displayAsHTML();
			exit();
		}

		/* TODO:
		if (ini_get('log_errors'))
			$this->log();
		*/
	}

	public function displayAsHTML() {
		echo '<hr />';

		echo get_class($this), ': ';

		echo $this->getMessage(), '<br />';

		printf("%s(%s)",
			$this->getFile(),
			$this->getLine());

		echo '<br />';
		echo '<br />';

		echo 'Backtrace:<br /><ol start="0">';

		foreach ($this->getTrace() as $entry) {
			echo '<li>';

			printf("%s(%s): %s%s%s(%s)",
				$entry['file'],
				$entry['line'],
				array_key_exists('class', $entry)? $entry['class']: '',
				array_key_exists('type', $entry)? $entry['type']: '',
				$entry['function'],
				array_key_exists('args', $entry) ? $entry['args'] : '');

			echo '</li>';
		}
		echo '</ol>';
	}
}


function swat_exception_handler($e) {
	$e->process();
}

set_exception_handler('swat_exception_handler');

?>
