<?php

/**
 * A what-you-see-is-what-you-get (WYSIWYG) XHTML textarea editor widget
 *
 * This textarea editor widget is powered by TinyMCE, which, like Swat is
 * licensed under the LGPL. See {@link http://tinymce.moxiecode.com/} for
 * details.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextareaEditor extends SwatTextarea
{
    // {{{ public properties

    /**
     * Width of the editor
     *
     * Specified in CSS units (percent, pixels, ems, etc). If not specified,
     * the {@link SwatTextarea::$cols} property will determine the width of
     * the editor.
     *
     * @var string
     */
    public $width = null;

    /**
     * Height of the editor
     *
     * Specified in CSS units (percent, pixels, ems, etc). If not specified,
     * the {@link SwatTextarea::$rows} property will determine the height of
     * the editor.
     *
     * @var string
     */
    public $height = null;

    /**
     * Base-Href
     *
     * Optional base-href, used to reference images and other urls in the
     * editor.
     *
     * @var string
     */
    public $base_href = null;

    /**
     * An optional JSON server used to provide uploaded image data to the
     * insert image dialog
     *
     * If specified, the insert image diaplog will show a list of thumbnails
     * from which the user can select an image to insert.
     *
     * The server should return a JSON response formatted as follows:
     * <code>
     * [
     *   {
     *     'id':     $image_id,
     *     'images': {
     *       $size_shortname : {
     *         'title':  $visible_size_title,
     *         'uri':    $uri_of_image_at_size,
     *         'width':  $width_of_image_at_size,
     *         'height': $height_of_image_at_size
     *       },
     *       ... more image sizes ...
     *     }
     *   },
     *   ... more images ...
     * ]
     * </code>
     *
     * The size shortname <i>pinky</i> must be present and should be 48x48
     * pixels. If a size shortname of <i>small</i> is present, it will be
     * selected as the default size.
     *
     * Other data my be returned by the JSON server, and will be ignored.
     *
     * @var string
     */
    public $image_server;

    /**
     * Base-Href
     *
     * @var string
     *
     * @deprecated Use {@link SwatTextareaEditor::$base_href} instead.
     */

    public $basehref = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new what-you-see-is-what-you-get XHTML textarea editor
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;
        $this->rows = 30;
    }

    // }}}
    // {{{ public function display()

    public function display()
    {
        if (!$this->visible) {
            return;
        }

        SwatWidget::display();

        // textarea tags cannot be self-closing when using HTML parser on XHTML
        $value = $this->value === null ? '' : $this->value;

        // escape value for display because we actually want to show entities
        // for editing
        $value = htmlspecialchars($value);

        $div_tag = new SwatHtmlTag('div');
        $div_tag->class = 'swat-textarea-editor-container';
        $div_tag->open();

        $textarea_tag = new SwatHtmlTag('textarea');
        $textarea_tag->name = $this->id;
        $textarea_tag->id = $this->id;
        $textarea_tag->class = $this->getCSSClassString();
        // NOTE: The attributes rows and cols are required in
        //       a textarea for XHTML strict.
        $textarea_tag->rows = $this->rows;
        $textarea_tag->cols = $this->cols;
        $textarea_tag->setContent($value);
        $textarea_tag->accesskey = $this->access_key;

        // set element styles if width and/or height are specified
        if ($this->width !== null || $this->height !== null) {
            if ($this->width !== null && $this->height !== null) {
                $textarea_tag->style = sprintf(
                    'width: %s; height: %s;',
                    $this->width,
                    $this->height
                );
            } elseif ($this->width !== null) {
                $textarea_tag->style = sprintf('width: %s;', $this->width);
            } else {
                $textarea_tag->style = sprintf('height: %s;', $this->height);
            }
        }

        if (!$this->isSensitive()) {
            $textarea_tag->disabled = 'disabled';
        }

        $textarea_tag->display();

        $div_tag->close();
    }

    // }}}
    // {{{ public function getFocusableHtmlId()

    /**
     * Gets the id attribute of the XHTML element displayed by this widget
     * that should receive focus
     *
     * @return string the id attribute of the XHTML element displayed by this
     *                 widget that should receive focus or null if there is
     *                 no such element.
     *
     * @see SwatWidget::getFocusableHtmlId()
     */
    public function getFocusableHtmlId()
    {
        return null;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this textarea
     *
     * @return array the array of CSS classes that are applied to this textarea.
     */
    protected function getCSSClassNames()
    {
        $classes = array('swat-textarea-editor');
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
