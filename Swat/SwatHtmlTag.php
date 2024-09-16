<?php

/**
 * Stores and outputs an HTML tag
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlTag extends SwatObject
{


    /**
     * The name of the HTML tag
     *
     * @var string
     */
    private $tag_name;

    /**
     * Atribute array
     *
     * Array containing attributes of the HTML tag in the form:
     *    attribute_name => value
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Optional content for the body of the XHTML tag
     *
     * @var string
     */
    private $content = null;

    /**
     * Optional content type for the body of the XHTML tag
     *     default text/plain
     *     use text/xml for XHTML fragments
     *
     * @var string
     */
    private $content_type = 'text/plain';



    /**
     * Creates a new HTML tag
     *
     * @param string $tag_name the name of the HTML tag.
     * @param array $attributes an optional array of attributes in the form:
     *                           attribute => value
     */
    public function __construct($tag_name, $attributes = null)
    {
        $this->tag_name = $tag_name;

        if (is_array($attributes)) {
            $this->attributes = $attributes;
        }
    }



    /**
     * Set content for the body of the XHTML tag
     *
     * This property is a UTF-8 encoded XHTML fragment. It is not escaped
     * before display so the user of SwatHtmlTag is responsible for any
     * escaping that must occur.
     *
     * When this value is set {@link SwatHtmlTag::display()} displays this
     * content after displaying the opening tag. Then it displays an explicit
     * closing tag.
     *
     * @param string $content content for the body of the XHTML tag
     * @param string $type mime type of the content.  Default is 'text/plain',
     *                      use 'text/xml' for XHTML fragments.
     */
    public function setContent($content, $type = 'text/plain')
    {
        $this->content = $content;
        $this->content_type = $type;
    }



    /**
     * Adds an array of attributes to this XHTML tag
     *
     * If entries in the attributes array coincide with existing attributes of
     * this XHTML tag, the attributes in the array overwrite the existing
     * attributes.
     *
     * @param array an array of attribute-value pairs of the form
     *               'attribute' => 'value'.
     */
    public function addAttributes($attributes)
    {
        if (is_array($attributes)) {
            $this->attributes = array_merge($this->attributes, $attributes);
        }
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
            $this->displayContent();
            $this->close();
        }
    }



    /**
     * Displays the content of this tag
     *
     * If {@link SwatHtmlTag::content} is set then the content is displayed.
     *
     * @see SwatHtmlTag::display()
     */
    public function displayContent()
    {
        if ($this->content !== null) {
            if ($this->content_type === 'text/plain') {
                echo SwatString::minimizeEntities($this->content);
            } else {
                echo $this->content;
            }
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
        echo '</', $this->tag_name, '>';
    }



    /**
     * Gets this tag as a string
     *
     * The string is the same as the displayed content of
     * {@link SwatHtmlString::display()}. It is not possible to get paired tags
     * as strings as this object has no knowledge of what is displayed between
     * the opening and closing tags.
     *
     * @see SwatHtmlTag::display()
     *
     * @return string this tag as a string.
     */
    public function toString(): string
    {
        ob_start();
        $this->display();
        return ob_get_clean();
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
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        } else {
            return null;
        }
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
            $value === null ? null : (string) $value;
    }



    /**
     * Gets this tag as a string
     *
     * This is a magic method that is called by PHP when this object is used
     * in string context. For example:
     *
     * <code>
     * $img = new SwatHtmlTag('img');
     * $img->alt = 'example image';
     * $img->src = 'http://example.com/example.png';
     * echo $img;
     * </code>
     *
     * Note: It is more efficient to simply call {@link SwatHtmlTag::display()}
     * instead of using <code>echo $tag;</code>.
     *
     * @return string this tag as a string.
     *
     * @see SwatHtmlTag::toString()
     */
    public function __toString(): string
    {
        return $this->toString();
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
        echo '<', $this->tag_name;

        foreach ($this->attributes as $attribute => $value) {
            if ($value !== null) {
                echo ' ',
                    $attribute,
                    '="',
                    SwatString::minimizeEntities($value),
                    '"';
            }
        }

        if ($self_closing) {
            echo ' />';
        } else {
            echo '>';
        }
    }

}
