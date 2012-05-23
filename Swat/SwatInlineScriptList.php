<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatObject.php';

/**
 * Collection of inline scripts
 *
 * @package   Swat
 * @copyright 2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInlineScriptList implements Countable, IteratorAggregate
{
	// {{{ protected properties

	/**
	 * Array containing the inline scripts in this list
	 *
	 * @var array
	 *
	 * @see SwatInlineScriptList::add()
	 */
	protected $scripts = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new inline script list
	 *
	 * @param SwatInlineScriptList $list optional. An existing list to
	 *                                    copy into the new list.
	 */
	public function __construct(SwatInlineScriptList $list = null)
	{
		if ($list instanceof SwatInlineScriptList) {
			$this->add($list);
		}
	}

	// }}}
	// {{{ public function add()

	/**
	 * Adds one or more inline scripts to this list
	 *
	 * @param string|array|SwatInlineScriptList an inline script or set of
	 *                                           inline script to add to this
	 *                                           list.
	 */
	public function add($script)
	{
		if ($script instanceof SwatInlineScriptList) {
			$this->scripts = array_merge(
				$this->scripts,
				$script->scripts
			);
		} elseif (is_array($script)) {
			$this->scripts = array_merge(
				$this->scripts,
				$script
			);
		} else {
			$this->scripts[] = $script;
		}
	}

	// }}}
	// {{{ public function count()

	/**
	 * Gets the number of inline scripts in this list
	 *
	 * Satisfies the countable itnerface.
	 *
	 * @return integer the number of inline scripts in this list
	 */
	public function count()
	{
		return count($this->scripts);
	}

	// }}}
	// {{{ public function getIterator()

	/**
	 * Gets an iterator for the inline scripts in this list
	 *
	 * Satisfies the IteratorAggregate interface.
	 *
	 * @return array an iterator for the inline scripts in this list.
	 */
	public function getIterator()
	{
		// Note: this returns a copy of the internal array by design.
		return $this->scripts;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays the inline scripts in this list
	 *
	 * The scripts are wrapped in an appropriately escaped inline script
	 * tag.
	 */
	public function display()
	{
		if (count($this->scripts) > 0) {
			echo '<script>', "\n//<![CDATA[\n";

			foreach ($this->scripts as $script) {
				echo $script;
				echo "\n";
			}

			echo "\n//]]>\n</script>";
		}
	}

	// }}}
}

?>
