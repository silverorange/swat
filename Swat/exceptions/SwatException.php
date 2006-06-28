<?php

/**
 * An exception in Swat
 *
 * Exceptions in Swat have handy methods for outputting nicely formed error
 * messages. Call SwatException::setupHandler() to register SwatException as
 * the PHP exception handler. The SwatException handler is able to handle all
 * sub-classes of Exception by internally wrapping non-SwatExceptions in a new
 * instance of a SwatException. This allows all exceptions to be nicely
 * formatted and processed consistently.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatException extends Exception
{
	// {{{ protected properties

	protected $backtrace = null;
	protected $class = null;

	// }}}
	// {{{ public function __construct()

	public function __construct($message = null, $code = 0)
	{
		if (is_object($message) && ($message instanceof Exception)) {
			$e = $message;
			$message = $e->getMessage();
			$code = $e->getCode();
			parent::__construct($message, $code);
			$this->file = $e->getFile();
			$this->line = $e->getLine();
			$this->backtrace = $e->getTrace();
			$this->class = get_class($e);
		} else {
			parent::__construct($message, $code);
		}
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this exception
	 *
	 * Processing involves displaying errors, logging errors and sending
	 * error message emails
	 */
	public function process($exit = true)
	{
		if ($this->backtrace === null)
			$this->backtrace = $this->getTrace();
	
		if ($this->class === null)
			$this->class = get_class($this);

		if (ini_get('display_errors')) {
			if (isset($_SERVER['REQUEST_URI']))
				echo $this->toXHTML();
			else
				echo $this->toString();
		}

		if (ini_get('log_errors'))
			$this->log();

		if ($exit)
			exit(1);
	}

	// }}}
	// {{{ public function log()

	/**
	 * Logs this exception
	 *
	 * The exception is logged to the webserver error log.
	 */
	public function log()
	{
		error_log($this->getSummary(), 0);
	}

	// }}}
	// {{{ public function getSummary()

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
			$this->class,
			$this->getFile(),
			$this->getLine());

		return ob_get_clean();
	}

	// }}}
	// {{{ public function toString()

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
			$this->class,
			$this->getMessage(),
			$this->getFile(),
			$this->getLine());

		echo "Stack Trace:\n";
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {

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

	// }}}
	// {{{ public function toXHTML()

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
				$this->class,
				nl2br($this->getMessage()),
				$this->getFile(),
				$this->getLine());

		echo 'Stack Trace:<br /><dl>';
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {

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

	// }}}
	// {{{ public static function handle()
	
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
		if ($e instanceof SwatException) {
			$e->process();
		} else {
			// wrap other exceptions in SwatExceptions
			$e = new SwatException($e);
			$e->process();
		}
	}

	// }}}
	// {{{ protected function getArguments()

	/**
	 * Formats a method call's arguments
	 *
	 * @param mixed an array of arguments or a single argument.
	 *
	 * @return string the arguments formatted into a comma delimited string.
	 */
	protected function getArguments($args)
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

	// }}}
	// {{{ protected function displayStyleSheet()

	/**
	 * Displays style sheet required for XHMTL exception formatting
	 *
	 * @todo separate this into a separate file
	 */
	protected function displayStyleSheet()
	{
		echo '<style>';
		echo ".swat-exception { border: 1px solid #d43; margin: 1em; font-family: sans-serif; background: #fff !important; z-index: 9999 !important; color: #000; text-align: left; min-width: 400px; }\n";
		echo ".swat-exception h3 { background: #e65; margin: 0; padding: 5px; border-bottom: 2px solid #d43; color: #fff; }\n";
		echo ".swat-exception-body { padding: 0.8em; }\n";
		echo ".swat-exception-message { margin-left: 2em; padding: 1em; }\n";
		echo ".swat-exception dt { float: left; margin-left: 1em; }\n";
		echo ".swat-exception dd { margin-bottom: 1em; }\n";
		echo '</style>';
	}

	// }}}
	// {{{ public static function setupHandler()

	/**
	 * Set the PHP exception handler to use SwatException
	 *
	 * @param string $class the exception class containing a static handle()
	 *	method.
	 */
	public static function setupHandler($class = 'SwatException')
	{
		set_exception_handler(array($class, 'handle'));
	}

	// }}}
}

?>
