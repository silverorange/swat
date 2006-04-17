<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatAutoloaderRule.php';

/**
 * Automatically requires PHP files for undefined classes
 *
 * This static class is responsible for resolving filenames from class names
 * of undefined classes. The PHP5 autoloader function is used to load files
 * based on rules defined in this static class.
 *
 * To add a new autoloader rule, use the {@link SwatAutoloader::addRule()}
 * method.
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatAutoloaderRule
 */
class SwatAutoloader extends SwatObject
{
	private static $rules = array();

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
	public static function addRule($regexp, $filename, $last = true)
	{
		SwatAutoloader::$rules[] =
			new SwatAutoloaderRule($regexp, $filename, $last);
	}

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

		foreach (SwatAutoloader::$rules as $rule) {
			$result = $rule->apply($class_name);
			if ($result !== null) {
				$filename = $result;
				if ($rule->isLast())
					break;
			}
		}

		return $filename;
	}
}

/*
 * Adds default Swat autoloader rules
 */
SwatAutoloader::addRule('/^SwatDB(.*)?Exception$/', 'SwatDB/exceptions/SwatDB$1Exception.php');
SwatAutoloader::addRule('/^SwatDB(.*)/', 'SwatDB/$1');
SwatAutoloader::addRule('/^Swat(.*)?Exception$/', 'Swat/exceptions/Swat$1Exception.php');
SwatAutoloader::addRule('/^Swat(.*)/', 'Swat/Swat$1.php');

/**
 * Provides an opportunity to define a class before causing a fatal error when
 * an undefined class is used
 *
 * This implementation uses the {@link SwatAutoloader} to require an
 * appropriate file containing a class definition for the undefined class.
 *
 * See the PHP documentation on {@link http://php.net/__autoload __autoload()}.
 *
 * @param string $class_name the name of the undefined class.
 */
function __autoload($class_name)
{
	$filename = SwatAutoloader::getFileFromClass($class_name);

	// We do not throw an exception here because is_callable() will break.

	if ($filename !== null)
		require $filename;
}

?>
