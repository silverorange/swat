<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Stores and outputs an HTML tag.
 */
class SwatHtmlTag extends SwatObject {

	/**
	 * @var string The name of the HTML tag.
	 */
	public $tagname;

	/**
	 * @var array Array containing attributes of the HTML tag in
	 * the form of attr_name => value.
	 */
	private $attributes;

	/**
	 * @param string $tagname The name of the HTML tag.
	 */
	function __construct($tagname) {
		$this->tagname = $tagname;
		$this->attributes = array();
	}

	function __get($attr) {
		if (isset($this->attributes[$attr]))
			return $this->attributes[$attr];
		else
			throw new SwatException(__CLASS__.": undefined attribute $attr");
	}

	function __set($attr, $val) {
		$this->attributes[$attr] = $val;
	}

	/**
	 * Remove an attribute.
	 *
	 * Remove a previously assigned attribute. Useful when one tag object is
	 * displayed multiple times with different attributes.
	 *
	 * @param string $attr The name of attribute to remove.
	 */
	function removeAttr($attr) {
		unset($this->attributes[$attr]);
	}

	/**
	 * Open the tag.
	 *
	 * Output the opening tag including all its attributes.
	 *
	 * @param bool $implicit_close If true the tag will be closed implicitly.
	 * Default false. It is preferrable to call display() rather than calling
	 * open() with this parameter.
	 */
	public function open($implicit_close = false) {
		echo '<', $this->tagname;

		if ($this->attributes != null) {
			foreach ($this->attributes as $attr => $value) {
				if ($value == null)
					echo ' ', $attr;
				else
					echo ' ', $attr, '="', $value, '"';
			}
		}

		if ($implicit_close)
			echo ' />';
		else
			echo '>';
	}

	/**
	 * Close the tag.
	 *
	 * Output the closing tag.
	 */
	public function close() {
		echo '</', $this->tagname, '>';
	}

	/**
	 * Display the tag.
	 *
	 * Output the opening tag including all its attributes and implicitly close the 
	 * tag.
	 */
	public function display() {
		$this->open(true);
	}
}

?>
