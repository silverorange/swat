<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Automatically requires PHP files for undefined classes
 *
 * This static class is responsible for resolving filenames from class names
 * of undefined classes. The PHP5 spl_autoload function is used to load files
 * based on rules defined in this static class.
 *
 * To add a new autoloader rule, use the {@link SwatAutoloader::addRule()}
 * method.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatAutoloaderRule
 */
class SwatAutoloader extends SwatObject
{
	// {{{ private properties

	private static $rules = array();

	// }}}
	// {{{ public static function addRule()

	/**
	 * Adds an autoloader rule to the autoloader
	 *
	 * @param string $expression the class name expression. Uses PERL regular
	 *                            expression syntax.
	 * @param string $replacement the format string of the filename for the
	 *                             class expression.
	 * @param boolean $last whether or not the added rule is final or not.
	 *
	 * @see SwatAutoloaderRule
	 */
	public static function addRule($expression, $replacement, $last = true)
	{
		self::$rules[] =
			new SwatAutoloaderRule($expression, $replacement, $last);
	}

	// }}}
	// {{{ public static function loadRules()

	/**
	 * Loads a list of autoloader rules from a file
	 *
	 * This format of the file is as follows:
	 *
	 * Each line defines a rule. The line is tokenized into fields based on
	 * whitespace characters (tab and space). The first field on the line is
	 * the rule expression. The next field on the line is the rule replacement.
	 * After the rule replacement field, there is an optional field
	 * to specify whether of not the rule is final. If this field is present
	 * and its value is 1, the rule is final. If the field is present and its
	 * value is 0 or if the field is omitted, the rule is not final.
	 * Lines beginning with a hash character (#) are ignored.
	 *
	 * An example file containing two rules is:
	 * <code>
	 * /^Swat(.*)/            Swat/Swat$1.php
	 * /^Swat(.*)?Exception$/ Swat/exceptions/Swat$1Exception.php 1
	 * </code>
	 *
	 * @param string $filename the name of the file from which to load the
	 *                          autoloader rules.
	 *
	 * @see SwatAutoloader::addRule()
	 */
	public static function loadRules($filename)
	{
		$rule_lines = file($filename);
		foreach ($rule_lines as $rule_line) {
			$rule_line = trim($rule_line);
			if (mb_substr($rule_line, 0, 1) === '#')
				continue;

			$last = false;

			$tok = strtok($rule_line, " \t");
			if ($tok === false)
				continue;

			$expression = $tok;
			$tok = strtok(" \t");
			if ($tok === false)
				continue;

			$replacement = $tok;
			$tok = strtok(" \t");
			if ($tok !== false)
				$last = ((integer)$tok === 1);

			self::addRule($expression, $replacement, $last);
		}
	}

	// }}}
	// {{{ public static function getFileFromClass()

	/**
	 * Gets the filename of a class name
	 *
	 * This method uses the autoloader's list of rules to find an appropriate
	 * filename for a class name. This is used by PHP5's __autoload() method
	 * to find an appropriate file for undefined classes.
	 *
	 * @param string $class_name the name of the class to get the filename for.
	 *
	 * @return string the name of the file that likely contains the class
	 *                 definition or null if no such filename could be
	 *                 determined.
	 */
	public static function getFileFromClass($class_name)
	{
		$filename = null;

		foreach (self::$rules as $rule) {
			$result = $rule->apply($class_name);
			if ($result !== null) {
				$filename = $result;
				if ($rule->isLast())
					break;
			}
		}

		return $filename;
	}

	// }}}
	// {{{ public static function autoload()

	/**
	 * Provides an opportunity to define a class before causing a fatal error
	 * when an undefined class is used
	 *
	 * If an appropriate file exists for the given class name, it is required.
	 *
	 * @param string $class_name the name of the undefined class.
	 */
	public static function autoload($class_name)
	{
		$filename = self::getFileFromClass($class_name);

		// We do not throw an exception here because is_callable() will break.

		if ($filename !== null) {
			require $filename;
		}
	}

	// }}}
	// {{{ public static function registerAutoload()

	/**
	 * Registers {@link SwatAutoload::autoload()} as an autoload function with
	 * the spl_autoload function
	 *
	 * See {@link http://ca.php.net/manual/en/function.spl-autoload-register.php
	 * the documentation on SPL autoloading} for details.
	 */
	public static function registerAutoload()
	{
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}

	// }}}
}

// Register SwatAutoloader as an autoloader.
SwatAutoloader::registerAutoload();

?>
