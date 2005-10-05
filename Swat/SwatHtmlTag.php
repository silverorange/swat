<?php

require_once 'Swat/SwatObject.php';

/**
 * Stores and outputs an HTML tag
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlTag extends SwatObject
{
	/**
	 * The name of the HTML tag
	 *
	 * @var string
	 */
	public $tagname;

	/**
	 * Atribute array
	 *
	 * Array containing attributes of the HTML tag in the form:
	 *    attribute_name => value
	 *
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Content (optional)
	 *
	 * Optional content for the body of the HTML tag. When this is set
	 * {@link SwatHtmlTag::display()} will output this content followed by an
	 * explicit closing tag.
	 *
	 * @var string
	 */
	public $content = null;

	/**
	 * Creates a new HTML tag
	 *
	 * @param string $tagname the name of the HTML tag.
	 * @param array $attributes an optional array of attributes in the form:
	 *                           attribute => value
	 */
	function __construct($tagname, $attributes = null)
	{
		$this->tagname = $tagname;

		if (is_array($attributes))
			$this->attributes = $attributes;
	}

	/**
	 * Magic __get method
	 *
	 * This should never be called directly, but is invoked indirectly when
	 * accessing properties of a tag object.
	 *
	 * @param string $attr the name of attribute to get.
	 *
	 * @return mixed the value of the attribute. If the attribute is not set,
	 *                null is returned.
	 */
	public function __get($attribute)
	{
		if (isset($this->attributes[$attribute]))
			return $this->attributes[$attribute];
		else
			return null;
	}

	/**
	 * Magic __set method
	 *
	 * This should never be called directly, but is invoked indirectly when
	 * setting properties of a tag object.
	 *
	 * @param string $attribute the name of attribute.
	 * @param mixed $value the value of attribute.
	 */
	public function __set($attribute, $value)
	{
		$this->attributes[$attribute] =
			($value === null) ? null : (string)$value;
	}

	/**
	 * Removes an attribute
	 *
	 * Removes a previously assigned attribute. Useful when one tag object is
	 * displayed multiple times with different attributes.
	 *
	 * @param string $attribute The name of attribute to remove.
	 */
	public function removeAttribute($attribute)
	{
		unset($this->attributes[$attribute]);
	}

	/**
	 * Displays this tag
	 *
	 * Output the opening tag including all its attributes and implicitly
	 * close the tag. If explicit closing is desired, use
	 * {@link SwatHtmlTag::open()} and {@link SwatHtmlTag::close()} instead.
	 * If {@link SwatHtmlTag::content} is set then the content is displayed
	 * between an opening and closing tag, otherwise a self-closing tag is
	 * displayed.
	 *
	 * @see SwatHtmlTag::open()
	 */
	public function display()
	{
		if ($this->content === null) {
			$this->openInternal(true);
		} else {
			$this->openInternal(false);
			echo $this->content;
			$this->close();
		}
	}

	/**
	 * Opens this tag
	 *
	 * Outputs the opening tag including all its attributes. Should be paired
	 * with a call to {@link SwatHtmlTag::close()}. If implicit closing
	 * is desired, use {@link SwatHtmlTag::display()} instead.
	 *
	 * @see SwatHtmlTag::close()
	 */
	public function open()
	{
		$this->openInternal(false);
	}

	/**
	 * Closes this tag
	 *
	 * Outputs the closing tag. Should be paired with a call to 
	 * {@link SwatHtmlTag::open()}.
	 *
	 * @see SwatHtmlTag::open()
	 */
	public function close()
	{
		echo '</', $this->tagname, '>';
	}

	/**
	 * Outputs opening tag and all attributes
	 *
	 * This is a helper method that does the attribute displaying when opening
	 * this tag. This method can also display self-closing XHTML tags.
	 *
	 * @param boolean $self_closing whether this tag should be displayed as a
	 *                               self-closing tag.
	 */
	private function openInternal($self_closing = false)
	{
		echo '<', $this->tagname;

		foreach ($this->attributes as $attribute => $value)
			if ($value !== null)
				echo ' ', $attribute, '="', $value, '"';

		if ($self_closing)
			echo ' />';
		else
			echo '>';
	}
}

?>
