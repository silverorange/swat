<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * A class autoloader rule
 *
 * Class autoloader rules define how class names map to filenames. An
 * autoloader rule consists of three parts:
 *
 * - the expression
 * - the replacement
 * - whether or not this rule is final
 *
 * The expression of an autoloader rule is a regular expression using PERL
 * syntax that matches a class name in PHP. For example, the expression
 * '/^Swat(*.)$/' matches all Swat classes.
 *
 * The replacement of an autoloader rule is a string representing the filename
 * format of classes matched by the autoloader expression. The filename format
 * may contain substitution markers denoted by $n where n is the n'th
 * captured parenthesized subpattern. For example, the replacement
 * 'Swat/Swat$1.php' represents the file naming convention for Swat class
 * files. The format of replacement strings is intentionally similar to
 * Apache rewrite rule syntax.
 *
 * Whether or not an autoloader rule is final determines what happens if a
 * class name matches a rule. If a rule is final, the class name is not checked
 * against any other rules once a match is found. Rules are final by default.
 *
 * @package   Swat
 * @copyright 2006-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatAutoloader
 */
class SwatAutoloaderRule extends SwatObject
{
	// {{{ private properties

	private $expression;
	private $replacement;
	private $last;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new class autoloader rule
	 *
	 * @param string $expression the expression of this rule. Uses PERL regular
	 *                            expression syntax.
	 * @param string $replacement the replacement format string of this rule.
	 * @param boolean $last whether or not this rule is final.
	 */
	public function __construct($expression, $replacement, $last = true)
	{
		$this->expression = $expression;
		$this->replacement = $replacement;
		$this->last = $last;
	}

	// }}}
	// {{{ public function isLast()

	/**
	 * Whether or not this rule is a final rule
	 *
	 * @return boolean whether or not this rule is a final rule.
	 */
	public function isLast()
	{
		return $this->last;
	}

	// }}}
	// {{{ public function apply()

	/**
	 * Applies this autoloader rule to a class name
	 *
	 * @param string $class_name the name of the class to apply this rule to.
	 *
	 * @return string the filename the class name maps to if the filename
	 *                 matches this rule, or null if the filename does not
	 *                 match this rule.
	 */
	public function apply($class_name)
	{
		$filename = null;

		$matches = array();
		$match = preg_match($this->expression, $class_name, $matches);

		if ($match == 1) {
			$filename = $this->replacement;
			for ($i = 1; $i < count($matches); $i++) {
				$needle = '$'.$i;
				$filename = str_replace($needle, $matches[$i], $filename);
			}
		}

		return $filename;
	}

	// }}}
}

?>
