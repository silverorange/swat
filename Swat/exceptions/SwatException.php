<?php

/**
 * An exception in Swat
 *
 * Exceptions in Swat have handy methods for outputting nicely formed error
 * messages.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
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
			if (isset($_SERVER['REQUEST_URI']))
				echo $this->toXHTML();
			else
				echo $this->toString();
		}

		if (ini_get('log_errors'))
			$this->log();

		exit;
	}

	/**
	 * Logs this exception
	 *
	 * The exception is logged to the webserver error log.
	 */
	public function log()
	{
		error_log($this->getSummary(), 0);
	}

	/**
	 * Gets a one-line short text summary of this exception
	 *
	 * This summary is useful for log entries and error email titles.
	 *
	 * @return string a one-line summary of this exception
	 */
	public function getSummary()
	{
		ob_start();

		printf("%s in file '%s' line %s",
			get_class($this),
			$this->getFile(),
			$this->getLine());

		return ob_get_clean();
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

		printf("Uncaught Exception: %s\n\nMessage:\n\t%s\n\n".
			"Thrown in file '%s' on line %s.\n\n",
			get_class($this),
			$this->getMessage(),
			$this->getFile(),
			$this->getLine());

		echo "Stack Trace:\n";
		$trace = $this->getTrace();
		$count = count($trace);

		foreach ($trace as $entry) {

			if (array_key_exists('args', $entry))
				$arguments = $this->getArguments($entry['args']);
			else
				$arguments = '';

			printf("%s. In file '%s' on line %s.\n%sMethod: %s%s%s(%s)\n",
				str_pad(--$count, 6, ' ', STR_PAD_LEFT),
				$entry['file'],
				$entry['line'],
				str_repeat(' ', 8),
				array_key_exists('class', $entry) ? $entry['class'] : '',
				array_key_exists('type', $entry) ? $entry['type'] : '',
				$entry['function'],
				$arguments);
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

		printf('<h3>Uncaught Exception: %s</h3>'.
				'<div class="swat-exception-body">'.
				'Message:<div class="swat-exception-message">%s</div>'.
				'Thrown in file <strong>%s</strong> '.
				'on line <strong>%s</strong>.<br /><br />',
				get_class($this),
				nl2br($this->getMessage()),
				$this->getFile(),
				$this->getLine());

		echo 'Stack Trace:<br /><dl>';
		$trace = $this->getTrace();
		$count = count($trace);

		foreach ($trace as $entry) {

			if (array_key_exists('args', $entry))
				$arguments = htmlentities($this->getArguments($entry['args']), null, 'UTF-8');
			else
				$arguments = '';

			printf('<dt>%s.</dt><dd>In file <strong>%s</strong> '.
				'line&nbsp;<strong>%s</strong>.<br />Method: '.
				'<strong>%s%s%s(</strong>%s<strong>)</strong></dd>',
				--$count,
				$entry['file'],
				$entry['line'],
				array_key_exists('class', $entry) ? $entry['class'] : '',
				array_key_exists('type', $entry) ? $entry['type'] : '',
				$entry['function'],
				$arguments);
		}

		echo '</dl></div></div>';

		return ob_get_clean();
	}
	
	/**
	 * Handles an exception
	 *
	 * Runs the process() method on SwatException exceptions and displays all
	 * other exceptions.
	 *
	 * @param Exception $e the exception to handle.
	 */
	public static function handle($e)
	{
		if ($e instanceof SwatException)
			$e->process();
		else
			echo $e;
	}

	/**
	 * Formats a method call's arguments
	 *
	 * @param mixed an array of arguments or a single argument.
	 *
	 * @return string the arguments formatted into a comma delimited string.
	 */
	private function getArguments($args)
	{
		if (is_array($args)) {
			foreach ($args as &$arg) {
				if (is_object($arg)) {
					$arg = '<'.get_class($arg).' object>';
				} elseif ($arg === null) {
					$arg = '<null>';
				} elseif (is_string($arg)) {
					$arg = "'".$arg."'";
				} elseif (is_array($arg)) {
					$arg = 'array('.$this->getArguments($arg).')';
				}
			}

			return implode(', ', $args);
		} else {
			return $args;
		}
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
 * Set the PHP5 exception handler to out cutom function
 */
set_exception_handler(array('SwatException', 'handle'));

?>
