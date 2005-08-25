<?php

/**
 * An exception in Swat
 *
 * Exceptions in Swat have handy methods for outputting nicely formed error
 * messages.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatException extends Exception
{
	/**
	 * Processes this exception
	 *
	 * Processing involves displaying errors, logging errors and sending
	 * error message emails
	 */
	public function process()
	{
		if (ini_get('display_errors')) {
			echo $this->toXHTML();
			exit;
		}

		/* TODO: make error logging work
		if (ini_get('log_errors'))
			$this->log();
		*/
	}

	/**
	 * Logs this exception
	 *
	 * The exception is logged to the webserver error log.
	 */
	public function log()
	{
	}

	/**
	 * Gets a one-line short text summar of this exception
	 *
	 * This summary is useful for log entries and error email titles.
	 *
	 * @return string a one-line summary of this exception
	 */
	public function getSummary()
	{
	}

	/**
	 * Gets this exception as a nicely formatted text block
	 *
	 * This is useful for text-based logs and emails.
	 *
	 * @return string this exception formatted as text.
	 */
	public function toString()
	{
		ob_start();
		
		echo "Uncaught Exception\n\n";

		printf("%s: %s\n\nThrown in file '%s' (on line %s)\n\n",
			get_class($this),
			$this->getMessage(),
			$this->getFile(),
			$this->getLine());

		echo "Stack Trace:\n";
		$trace = $this->getTrace();
		$count = count($trace);

		foreach ($trace as $entry) {
			printf("\t%d. In file '%s' (on line %s) in method: %s%s%s(%s)\n",
				--$count,
				$entry['file'],
				$entry['line'],
				array_key_exists('class', $entry)? $entry['class']: '',
				array_key_exists('type', $entry)? $entry['type']: '',
				$entry['function'],
				array_key_exists('args', $entry) ? $entry['args'] : '');
		}
		
		echo "\n";

		return ob_get_clean();
	}

	/**
	 * Gets this exception as a nicely formatted XHTML fragment
	 *
	 * This is nice for debugging errors on a staging server.
	 *
	 * @return string this exception formatted as XHTML.
	 */
	public function toXHTML()
	{
		ob_start();

		$this->displayStyleSheet();

		echo '<div class="swat-exception">';
	
		printf('<h3>Uncaught Exception: %s</h3><div class="swat-exception-body">'.
				'Message:<div class="swat-exception-message">%s</div>Thrown in file <strong>%s</strong> '.
				'on line <strong>%s</strong>.<br /><br />',
				get_class($this),
				$this->getMessage(),
				$this->getFile(),
				$this->getLine());

		echo 'Stack Trace:<br /><dl>';
		$trace = $this->getTrace();
		$count = count($trace);

		foreach ($trace as $entry) {
			
			if (array_key_exists('args', $entry)) {
				if (is_array($entry['args'])) {
					foreach ($entry['args'] as &$arg)
						if (is_object($arg))
							$arg = get_class($arg);

					$arguments = implode(', ', $entry['args']);
				} else {
					$arguments = $entry['args'];
				}
			} else {
				$arguments = '';
			}
			
			printf("<dt>%s.</dt><dd>In file <strong>%s</strong> ".
				"line&nbsp;<strong>%s</strong>.<br />Method: ".
				"<strong>%s%s%s(%s)</strong></dd>",
				--$count,
				$entry['file'],
				$entry['line'],
				array_key_exists('class', $entry)? $entry['class']: '',
				array_key_exists('type', $entry)? $entry['type']: '',
				$entry['function'],
				$arguments);
		}
		
		echo '</dl></div></div>';

		return ob_get_clean();
	}

	/**
	 * Displays style sheet required for XHMTL exception formatting
	 *
	 * @todo separate this into a separate file
	 */
	private function displayStyleSheet()
	{
		echo '<style>';
		echo ".swat-exception { border: 1px solid #d43; margin: 1em; font-family: sans-serif; }\n";
		echo ".swat-exception h3 { background: #e65; margin: 0; padding: 5px; border-bottom: 2px solid #d43; color: #fff; }\n";
		echo ".swat-exception-body { padding: 0.8em; }\n";
		echo ".swat-exception-message { margin-left: 2em; padding: 1em; }\n";
		echo ".swat-exception dt { float: left; margin-left: 1em; }\n";
		echo ".swat-exception dd { margin-bottom: 1em; }\n";
		echo '</style>';
	}
}

/**
 * Handles PHP5 exceptions
 *
 * Runs the process() method on SwatException exceptions and displays all
 * other exceptions.
 */
function swat_exception_handler($e)
{
	if ($e instanceof SwatException)
		$e->process();
	else
		echo $e;
}

/**
 * Set the PHP5 exception handler to out cutom function
 */
set_exception_handler('swat_exception_handler');

?>
