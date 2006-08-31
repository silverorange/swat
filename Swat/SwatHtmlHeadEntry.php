<?php

require_once 'Swat/SwatObject.php';

/**
 * Stores and outputs an HTML head entry
 *
 * Head entries are things like scripts and styles that belong in the HTML
 * head section.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatHtmlHeadEntry extends SwatObject
{
	// {{{ protected properties

	/**
	 * The uri of this head entry
	 *
	 * @var string
	 */
	protected $uri = '';

	/**
	 * The package this HTML head entry belongs to.
	 *
	 * When HTML head entries are displayed, they are grouped by package and
	 * groups are order by inter-package dependencies.
	 *
	 * @var string
	 */
	protected $package_id = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $uri the uri of the entry.
	 * @param integer $package_id the package id of the package this HTML head
	 *                             entry belongs to.
	 */
	public function __construct($uri, $package_id = null)
	{
		$this->uri = $uri;
		$this->package_id = $package_id;
	}

	// }}}
	// {{{ public abstract function display()

	/**
	 * Displays this html head entry
	 *
	 * Entries are displayed differently based on type.
	 *
	 * @param string $uri_prefix an optional string to prefix the URI with.
	 */
	public abstract function display($uri_prefix = '');

	// }}}
	// {{{ public abstract function displayInline($path)

	/**
	 * Displays the resource referenced by this html head entry inline
	 *
	 * Entries are displayed differently based on type.
	 *
	 * @param string $path the path containing the resource files.
	 */
	public abstract function displayInline($path);

	// }}}
	// {{{ public function getUri()

	/**
	 * Gets the URI of this HTML head entry
	 *
	 * @return string the URI of this HTML head entry.
	 */
	public function getUri()
	{
		return $this->uri;
	}

	// }}}
	// {{{ public function getType()

	/**
	 * Gets the type of this HTML head entry
	 *
	 * @return string the type of this HTML head entry.
	 */
	public function getType()
	{
		return get_class($this);
	}

	// }}}
	// {{{ public function getPackageId()

	/**
	 * Gets the package id of this HTML head entry
	 *
	 * @return string the package id of this HTML head entry.
	 */
	public function getPackageId()
	{
		return $this->package_id;
	}

	// }}}
}

?>
