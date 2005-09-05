<?php

require_once 'Swat/SwatObject.php';

/**
 * Base class for a layout
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatLayout extends SwatObject
{
	// {{{ private properties

	private $_properties = array();
	private $_filename = null;
	
	// }}}
	// {{{ public function __construct()

	public function __construct($filename)
	{
		$this->_filename = $filename;
	}

	// }}}
	// {{{ public function __get()

	public function __get($name)
	{
		if (!isset($this->_properties[$name]))
			throw new SwatException("There is no content available for '$name'.");

		return $this->_properties[$name];
	}

	// }}}
	// {{{ public function __set()

	public function __set($name, $content)
	{
		$this->_properties[$name] = $content;
	}

	// }}}
	// {{{ public function setFilename()

	public function setFilename($filename)
	{
		$this->_filename = $filename;
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		require_once $this->_filename;
	}

	// }}}
}
?>
