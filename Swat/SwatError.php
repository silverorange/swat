<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatErrorDisplayer.php';
require_once 'Swat/SwatErrorLogger.php';

/**
 * An error in Swat
 *
 * Unlike {@link SwatException} objects, errors do not interrupt the flow of
 * execution and can not be caught. Errors in Swat have handy methods for
 * outputting nicely formed error messages and logging errors.
 *
 * Like {@link SwatException}, SwatError filters sensitive data from stack
 * trace parameters. See the class-level documentation of SwatException for
 * details on how this works.
 *
 * @package   Swat
 * @copyright 2006-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatError
{
	// {{{ protected properties

	/**
	 * The message of this error
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * The severity of this error
	 *
	 * Error severity should be one of the E_* constants defined by PHP.
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var integer
	 */
	protected $severity;

	/**
	 * The file this error occurred in
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * The line this error occurred at
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var integer
	 */
	protected $line;

	/**
	 * The backtrace of this error
	 *
	 * This should be an array of the form provided by the built-in PHP
	 * function debug_backtrace().
	 *
	 * Set in {@link SwatError::__construct()}
	 *
	 * @var array
	 */
	protected $backtrace;

	/**
	 * @var SwatErrorDisplayer
	 */
	protected static $displayer = null;

	/**
	 * @var SwatErrorLogger
	 */
	protected static $logger = null;

	/**
	 * @var integer
	 */
	protected static $fatal_severity = E_USER_ERROR;

	// }}}
	// {{{ public static function setLogger()

	/**
	 * Sets the object that logs SwatError objects when they are processed
	 *
	 * For example:
	 * <code>
	 * SwatError::setLogger(new CustomLogger());
	 * </code>
	 *
	 * @param SwatErrorLogger $logger the object to use to log exceptions.
	 */
	public static function setLogger(SwatErrorLogger $logger)
	{
		self::$logger = $logger;
	}

	// }}}
	// {{{ public static function setDisplayer()

	/**
	 * Sets the object that displays SwatError objects when they are
	 * processed
	 *
	 * For example:
	 * <code>
	 * SwatError::setDisplayer(new SilverorangeDisplayer());
	 * </code>
	 *
	 * @param SwatErrorDisplayer $displayer the object to use to display
	 *                                           exceptions.
	 */
	public static function setDisplayer(SwatErrorDisplayer $displayer)
	{
		self::$displayer = $displayer;
	}

	// }}}
	// {{{ public static function setFatalSeverity()

	/**
	 * Sets the severity of SwatError that should be fatal
	 *
	 * @param integer $severity a bitwise combination of PHP error severities
	 *                           to be considered fatal.
	 */
	public static function setFatalSeverity($severity)
	{
		self::$fatal_severity = $severity;
	}

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new error object
	 *
	 * Error objects contain methods to display and log all types of errors
	 * that may occur.
	 *
	 * @param integer $severity the error code of this error. This should
	 *                           be one of the E_* constants set by PHP. See
	 *                           {@link
	 *                           http://php.net/manual/en/ref.errorfunc.php
	 *                           Error Handling and Logging Functions}.
	 * @param string $message the error message of this error.
	 * @param string $file the name of the file this error occurred in.
	 * @param integer $line the line number this error occurred at.
	 */
	public function __construct($severity, $message, $file, $line)
	{
		$backtrace = debug_backtrace();
		// remove this method call and the handle() call from the backtrace
		array_shift($backtrace);
		array_shift($backtrace);

		$this->message = $message;
		$this->severity = $severity;
		$this->file = $file;
		$this->line = $line;
		$this->backtrace = &$backtrace;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this error
	 *
	 * Processing involves displaying errors, logging errors and sending
	 * error message emails.
	 *
	 * If a fatal error has occured, this calls exit to ensure no further
	 * processing is done.
	 */
	public function process()
	{
		if (ini_get('display_errors'))
			$this->display();

		if (ini_get('log_errors'))
			$this->log();

		if ($this->severity & self::$fatal_severity)
			exit(1);
	}

	// }}}
	// {{{ public function getMessage()

	/**
	 * Gets the original message string of this error
	 *
	 * @return string original message of this error.
	 */
	public function getMessage()
	{
		return $this->message;
	}

	// }}}
	// {{{ public function getSeverity()

	/**
	 * Gets the severity of this error
	 *
	 * @return integer severity value as E_* PHP constant.
	 */
	public function getSeverity()
	{
		return $this->severity;
	}

	// }}}
	// {{{ public function log()

	/**
	 * Logs this error
	 *
	 * The error is logged to the webserver error log.
	 */
	public function log()
	{
		if (self::$logger === null) {
			error_log($this->getSummary(), 0);
		} else {
			$logger = self::$logger;
			$logger->log($this);
		}
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (self::$displayer === null) {
			if (isset($_SERVER['REQUEST_URI']))
				echo $this->toXHTML();
			else
				echo $this->toString();
		} else {
			$displayer = self::$displayer;
			$displayer->display($this);
		}
	}

	// }}}
	// {{{ public function getSummary()

	/**
	 * Gets a one-line short text summary of this error
	 *
	 * This summary is useful for log entries and error email titles.
	 *
	 * @return string a one-line summary of this error.
	 */
	public function getSummary()
	{
		ob_start();

		printf("%s error in file '%s' line %s",
			$this->getSeverityString(),
			$this->file,
			$this->line);

		return ob_get_clean();
	}

	// }}}
	// {{{ public function toString()

	/**
	 * Gets this error as a nicely formatted text block
	 *
	 * This is useful for text-based logs and emails.
	 *
	 * @return string this error formatted as text.
	 */
	public function toString()
	{
		ob_start();

		printf("%s:\n\nMessage:\n\t%s\n\n".
			"In file '%s' on line %s.\n\n",
			$this->getSeverityString(),
			$this->message,
			$this->file,
			$this->line);

		echo "Stack Trace:\n";
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {
			$class = array_key_exists('class', $entry) ?
				$entry['class'] : null;

			$function = array_key_exists('function', $entry) ?
				$entry['function'] : null;


			if (array_key_exists('args', $entry))
				$arguments = $this->getArguments(
					$entry['args'], $function, $class);
			else
				$arguments = '';

			printf("%s. In file '%s' on line %s.\n%sMethod: %s%s%s(%s)\n",
				str_pad(--$count, 6, ' ', STR_PAD_LEFT),
				array_key_exists('file', $entry) ? $entry['file'] : 'unknown',
				array_key_exists('line', $entry) ? $entry['line'] : 'unknown',
				str_repeat(' ', 8),
				($class === null) ? '' : $class,
				array_key_exists('type', $entry) ? $entry['type'] : '',
				($function === null) ? '' : $function,
				$arguments);
		}

		echo "\n";

		return ob_get_clean();
	}

	// }}}
	// {{{ public function toXHTML()

	/**
	 * Gets this error as a nicely formatted XHTML fragment
	 *
	 * This is nice for debugging errors on a staging server.
	 *
	 * @return string this error formatted as XHTML.
	 */
	public function toXHTML()
	{
		ob_start();

		$this->displayStyleSheet();

		echo '<div class="swat-exception">';

		printf('<h3>%s</h3>'.
				'<div class="swat-exception-body">'.
				'Message:<div class="swat-exception-message">%s</div>'.
				'Occurred in file <strong>%s</strong> '.
				'on line <strong>%s</strong>.<br /><br />',
				$this->getSeverityString(),
				nl2br(htmlspecialchars($this->message)),
				$this->file,
				$this->line);

		echo 'Stack Trace:<br /><dl>';
		$count = count($this->backtrace);

		foreach ($this->backtrace as $entry) {
			$class = array_key_exists('class', $entry) ?
				$entry['class'] : null;

			$function = array_key_exists('function', $entry) ?
				$entry['function'] : null;


			if (array_key_exists('args', $entry))
				$arguments = htmlspecialchars($this->getArguments(
					$entry['args'], $function, $class),
					null, 'UTF-8');
			else
				$arguments = '';

			printf('<dt>%s.</dt><dd>In file <strong>%s</strong> '.
				'line&nbsp;<strong>%s</strong>.<br />Method: '.
				'<strong>%s%s%s(</strong>%s<strong>)</strong></dd>',
				--$count,
				array_key_exists('file', $entry) ? $entry['file'] : 'unknown',
				array_key_exists('line', $entry) ? $entry['line'] : 'unknown',
				($class === null) ? '' : $class,
				array_key_exists('type', $entry) ? $entry['type'] : '',
				($function === null) ? '' : $function,
				$arguments);
		}

		echo '</dl></div></div>';

		return ob_get_clean();
	}

	// }}}
	// {{{ public static function handle()

	/**
	 * Handles an error
	 *
	 * When an error occurs, a SwatError object is created and processed.
	 *
	 * @param integer $errno the severity code of the handled error.
	 * @param string $errstr the message of the handled error.
	 * @param string $errfile the file ther handled error occurred in.
	 * @param integer $errline the line the handled error occurred at.
	 */
	public static function handle($errno, $errstr, $errfile, $errline)
	{
		// only handle error if error reporting is not suppressed
		if (error_reporting() != 0) {
			$error = new SwatError($errno, $errstr, $errfile, $errline);
			$error->process();
		}
	}

	// }}}
	// {{{ protected function getArguments()

	/**
	 * Formats a method call's arguments
	 *
	 * This method is also responsible for filtering sensitive parameters
	 * out of the final stack trace.
	 *
	 * @param array $args an array of arguments.
	 * @param string $method optional. The current method or function.
	 * @param string $class optional. The current class name.
	 *
	 * @return string the arguments formatted into a comma delimited string.
	 */
	protected function getArguments($args, $function = null, $class = null)
	{
		$params = array();
		$method = null;

		// try to get function or method parameter list using reflection
		if ($class !== null && $function !== null && class_exists($class)) {
			$class_reflector = new ReflectionClass($class);
			if ($class_reflector->hasMethod($function)) {
				$method = $class_reflector->getMethod($function);
				$params = $method->getParameters();
			}
		} elseif ($function !== null && function_exists($function)) {
			$method = new ReflectionFunction($function);
			$params = $method->getParameters();
		}

		// display each parameter
		$formatted_values = array();
		for ($i = 0; $i < count($args); $i++) {
			$value = $args[$i];

			if ($method !== null && array_key_exists($i, $params)) {
				$name = $params[$i]->getName();
				$sensitive = $this->isSensitiveParameter($method, $name);
			} else {
				$name = null;
				$sensitive = false;
			}

			if ($name !== null && $sensitive) {
				$formatted_values[] =
					$this->formatSensitiveParam($name, $value);
			} else {
				$formatted_values[] = $this->formatValue($value);
			}
		}

		return implode(', ', $formatted_values);
	}

	// }}}
	// {{{ protected function formatSensitiveParam()

	/**
	 * Removes sensitive information from a parameter value and formats
	 * the parameter as a string
	 *
	 * This is used, for example, to filter credit/debit card numbers from
	 * stack traces. By default, a string of the form
	 * "[$<i>$name</i> FILTERED]" is returned.
	 *
	 * @param string $name the name of the parameter.
	 * @param mixed $value the sensitive value of the parameter.
	 *
	 * @return string the filtered formatted version of the parameter.
	 *
	 * @see SwatException::$sensitive_param_names
	 */
	protected function formatSensitiveParam($name, $value)
	{
		return '[$'.$name.' FILTERED]';
	}

	// }}}
	// {{{ protected function formatValue()

	/**
	 * Formats a parameter value for display in a stack trace
	 *
	 * @param mixed $value the value of the parameter.
	 *
	 * @return string the formatted version of the parameter.
	 */
	protected function formatValue($value)
	{
		$formatted_value = '<unknown parameter type>';

		if (is_object($value)) {
			$formatted_value = '<'.get_class($value).' object>';
		} elseif ($value === null) {
			$formatted_value = '<null>';
		} elseif (is_string($value)) {
			$formatted_value = "'".$value."'";
		} elseif (is_int($value) || is_float($value)) {
			$formatted_value = strval($value);
		} elseif (is_bool($value)) {
			$formatted_value = ($value) ? 'true' : 'false';
		} elseif (is_resource($value)) {
			$formatted_value = '<resource>';
		} elseif (is_array($value)) {
			// check whether or not array is associative
			$keys = array_keys($value);
			$associative = false;
			$count = 0;
			foreach ($keys as $key) {
				if ($key !== $count) {
					$associative = true;
					break;
				}
				$count++;
			}

			$formatted_value = 'array(';

			$count = 0;
			foreach ($value as $key => $the_value) {
				if ($count > 0) {
					$formatted_value.= ', ';
				}

				if ($associative) {
					$formatted_value.= $this->formatValue($key);
					$formatted_value.= ' => ';
				}
				$formatted_value.= $this->formatValue($the_value);
				$count++;
			}
			$formatted_value.= ')';
		}

		return $formatted_value;
	}

	// }}}
	// {{{ protected function displayStyleSheet()

	/**
	 * Displays styles required to show XHTML error messages
	 *
	 * The styles are only output once even if multiple errors are displayed
	 * during one request.
	 */
	protected function displayStyleSheet()
	{
		static $style_sheet_displayed = false;

		if (!$style_sheet_displayed) {
			echo "<style>".
				".swat-exception { border: 1px solid #d43; margin: 1em; ".
				"font-family: sans-serif; background: #fff !important; ".
				"z-index: 9999 !important; color: #000; text-align: left; ".
				"min-width: 400px; }\n";

			echo ".swat-exception h3 { background: #e65; margin: 0; padding: ".
				"border-bottom: 1px solid #d43; color: #fff; }\n";

			echo ".swat-exception-body { padding: 0.8em; }\n";
			echo ".swat-exception-message { margin-left: 2em; padding: 1em; ".
				"}\n";

			echo ".swat-exception dt { float: left; margin-left: 1em; }\n";
			echo ".swat-exception dd { margin-bottom: 1em; }\n";
			echo '</style>';
			$style_sheet_displayed = true;
		}
	}

	// }}}
	// {{{ protected function getSeverityString()

	/**
	 * Gets a string representation of this error's severity
	 *
	 * @return string a string representation of this error's severity.
	 */
	protected function getSeverityString()
	{
		static $error_types = array(
			E_WARNING         => 'Warning',
			E_NOTICE          => 'Notice',
			E_USER_ERROR      => 'User Fatal Error',
			E_USER_WARNING    => 'User Warning',
			E_USER_NOTICE     => 'User Notice',
			E_STRICT          => 'Forward Compatibility Notice'
		);

		$out = null;
		if (isset($error_types[$this->severity]))
			$out = $error_types[$this->severity];

		return $out;
	}

	// }}}
	// {{{ protected function isSensitiveParameter()

	/**
	 * Detects whether or not a parameter is sensitive from the method-level
	 * documentation of the parameter's method
	 *
	 * Parameters with the following docblock tag are considered sensitive:
	 * <code>
	 * <?php
	 * /**
	 *  * @sensitive $parameter_name
	 *  *\/
	 * ?>
	 * </code>
	 *
	 * @param ReflectionFunctionAbstract $method the method the parameter to
	 *                                            which the parameter belongs.
	 * @param string $name the name of the parameter.
	 *
	 * @return boolean true if the parameter is sensitive and false if the
	 *                  method is not sensitive.
	 */
	protected function isSensitiveParameter(ReflectionFunctionAbstract $method,
		$name)
	{
		$sensitive = false;

		$exp =
			'/^.*@sensitive\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*).*$/';

		$documentation = $method->getDocComment();
		$documentation = str_replace("\r", "\n", $documentation);
		$documentation_exp = explode("\n", $documentation);
		foreach ($documentation_exp as $documentation_line) {
			$matches = array();
			if (preg_match($exp, $documentation_line, $matches) == 1 &&
				$matches[1] == $name) {
				$sensitive = true;
				break;
			}
		}

		return $sensitive;
	}

	// }}}
	// {{{ public static function setupHandler()

	/**
	 * Set the PHP error handler to use SwatError
	 */
	public static function setupHandler()
	{
		/*
		 * All run-time errors as specified in the error_reporting directive
		 * are handled.
		 */
		set_error_handler(array('SwatError', 'handle'), error_reporting());
	}

	// }}}
}

?>
