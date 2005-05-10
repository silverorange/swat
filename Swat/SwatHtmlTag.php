<?php

require_once('Swat/SwatObject.php');

/**
 * Stores and outputs an HTML tag
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatHtmlTag extends SwatObject {

	/**
	 * The name of the HTML tag
	 * @var string
	 */
	public $tagname;

	/**
	 * Atribute array
	 *
	 * Array containing attributes of the HTML tag in the form of
	 * (attr name) => (value).
	 * @var array
	 */
	private $attributes;

	/**
	 * Content (optional)
	 *
	 * Optional content for the body of the HTML tag. When this is set
	 * {@link SwatHtmlTag::display()} will output this content followed by an
	 * explicit closing tag.
	 * @var string
	 */
	public $content = null;

	/**
	 * @param string $tagname The name of the HTML tag.
	 */
	function __construct($tagname, $attributes = null) {
		$this->tagname = $tagname;

		if ($attributes !== null)
			$this->attributes = $attributes;
		else
			$this->attributes = array();
	}

	/**
	 * Magic __get method
	 *
	 * This should not be called directly, but is invoked indirectly when
	 * accessing properties of a tag object.
	 * @param string $attr The name of attribute.
	 */
	public function __get($attr) {
		if (isset($this->attributes[$attr]))
			return $this->attributes[$attr];
		else
			throw new SwatException(__CLASS__.": undefined attribute $attr");
	}

	/**
	 * Magic __set method
	 *
	 * This should not be called directly, but is invoked indirectly when
	 * setting properties of a tag object.
	 * @param string $attr The name of attribute.
	 * @param mixed $val The value of attribute.
	 */
	public function __set($attr, $val) {
		$this->attributes[$attr] = ($val === null) ? null : (string)$val;
	}

	/**
	 * Remove an attribute
	 *
	 * Remove a previously assigned attribute. Useful when one tag object is
	 * displayed multiple times with different attributes.
	 *
	 * @param string $attr The name of attribute to remove.
	 */
	public function removeAttr($attr) {
		unset($this->attributes[$attr]);
	}

	/**
	 * Display the tag
	 *
	 * Output the opening tag including all its attributes and implicitly close
	 * the tag.  If explicit closing is desired, use
	 * {@link SwatHtmlTag::display()} instead. If {@link SwatHtmlTag::content}
	 * is set then explicit closing is used and {@link SwatHtmlTag::content} is
	 * output within the tag.
	 */
	public function display() {
		if ($this->content === null) {
			$this->openInternal(true);
		} else {
			$this->openInternal(false);
			echo $this->content;
			$this->close();
		}
	}

	/**
	 * Open the tag
	 *
	 * Output the opening tag including all its attributes. Should be paired
	 * with a call to {@link SwatHtmlTag::close()}.  If implicit closing
	 * is desired, use {@link SwatHtmlTag::display()} instead.
	 */
	public function open() {
		$this->openInternal(false);
	}

	/**
	 * Close the tag
	 *
	 * Output the closing tag. Should be paired with a call to 
	 * {@link SwatHtmlTag::close()}.
	 */
	public function close() {
		echo '</', $this->tagname, '>';
	}

	private function openInternal($implicit_close) {
		echo '<', $this->tagname;

		if ($this->attributes !== null) {
			foreach ($this->attributes as $attr => $value) {
				if ($value !== null) {
					echo ' ', $attr, '="', htmlspecialchars($value), '"';
				}
			}
		}

		if ($implicit_close)
			echo ' />';
		else
			echo '>';
	}

}

?>
