<?php

/**
 * Image preview display control
 *
 * This control displays an image and uses a lightbox-like effect to display
 * another image when the first image is clicked.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImagePreviewDisplay extends SwatImageDisplay
{
    // {{{ public properties

    /**
     * Preview Image
     *
     * The src attribute in the XHTML img tag.
     *
     * @var string
     */
    public $preview_image;

    /**
     * Optional array of values to substitute into the preview image property
     *
     * Uses vsprintf() syntax, for example:
     *
     * <code>
     * $my_image->preview_image = 'mydir/%s.%s';
     * $my_image->preview_image_values = array('myfilename', 'ext');
     * </code>
     *
     * @var array
     */
    public $preview_image_values = [];

    /**
     * Preview Image height
     *
     * The height attribute in the XHTML img tag.
     *
     * @var integer
     */
    public $preview_height = null;

    /**
     * Preview Image width
     *
     * The width attribute in the XHTML img tag.
     *
     * @var integer
     */
    public $preview_width = null;

    /**
     * Whether or not to show a resize icon next to the image
     *
     * @var boolean
     */
    public $show_icon = true;

    /**
     * Whether or not to visibly display the title below the image
     *
     * By default no visible title is displayed.
     *
     * The visible title is only displayed if JavaScript is enabled. The
     * default title is "View Larger Image", but this may be changed by setting
     * the {@link SwatImageDisplay::$title} property of this image preview
     * display.
     *
     * @var boolean
     */
    public $show_title = false;

    /**
     * The href attribute in the XHTML anchor tag
     *
     * If JavaScript is not enabled, the image preview display will link to
     * this location
     *
     * Optionally uses vsprintf() syntax, for example:
     * <code>
     * $renderer->link = 'MySection/MyPage/%s?id=%s';
     * </code>
     *
     * @var string
     *
     * @see SwatImagePreviewDisplay::$link_value
     */
    public $link;

    /**
     * A value or array of values to substitute into the link
     *
     * The value property may be specified either as an array of values or as
     * a single value. If an array is passed, a call to vsprintf() is done
     * on the {@link SwatImageLinkCellRenderer::$link} property. If the value
     * is a string a single sprintf() call is made.
     *
     * @var mixed
     *
     * @see SwatImagePreviewDisplay::$link
     */
    public $link_value = null;

    /**
     * Optional container width (default is the image width + padding)
     */
    public $container_width = null;

    /**
     * Optional container height (default is the image height + padding)
     */
    public $container_height = null;

    /**
     * Optional title to display above the large image when the preview is
     * opened
     *
     * @var string
     */
    public $preview_title = null;

    /**
     * Only show the preview image when the preview area is smaller or equal to
     * the original area.
     *
     * @var boolean
     */
    public $show_preview_when_smaller = false;

    /**
     * Close text
     *
     * @var string
     */
    public $close_text = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new image preview display
     *
     * @param string $id a non-visible unique id for this widget.
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $yui = new SwatYUI(['dom', 'event']);
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addJavaScript(
            'packages/swat/javascript/swat-z-index-manager.js',
        );

        $this->addJavaScript(
            'packages/swat/javascript/swat-image-preview-display.js',
        );

        $this->addStyleSheet(
            'packages/swat/styles/swat-image-preview-display.css',
        );

        $this->title = Swat::_('View Larger Image');
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this image
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        if (!$this->isPreviewDisplayable()) {
            parent::display();
        } else {
            if ($this->link !== null) {
                $tag = new SwatHtmlTag('a');
                if ($this->link_value === null) {
                    $tag->href = $this->link;
                } elseif (is_array($this->link_value)) {
                    $tag->href = vsprintf($this->link, $this->link_value);
                } else {
                    $tag->href = sprintf($this->link, $this->link_value);
                }
            } else {
                $tag = new SwatHtmlTag('span');
            }

            $tag->id = $this->id . '_wrapper';
            $tag->title = $this->title;

            if ($this->show_icon) {
                $tag->class = 'swat-image-preview-display-link';
            } else {
                $tag->class = 'swat-image-preview-display-link-plain';
            }

            $tag->open();
            parent::display();
            $tag->close();

            Swat::displayInlineJavaScript($this->getInlineJavaScript());
        }
    }

    // }}}
    // {{{ protected function isPreviewDisplayable()

    /**
     * Checks whether the preview exists, and whether it should be displayed.
     *
     * @return boolean True if the preview should be displayed, false if it
     *                  should not.
     */
    protected function isPreviewDisplayable()
    {
        $image_area = $this->width * $this->height;
        $preview_area = $this->preview_width * $this->preview_height;

        $difference = ($preview_area - $image_area) / $image_area;

        return $this->preview_image != '' &&
            ($this->show_preview_when_smaller || $difference >= 0.2);
    }

    // }}}
    // {{{ protected function getJavaScriptClass()

    /**
     * Gets the name of the JavaScript class to instantiate for this image
     * preview display
     *
     * Sub-classes of this class may want to return a sub-class of the default
     * JavaScript image preview class.
     *
     * @return string the name of the JavaScript class to instantiate for this
     *                 image preview display. Defaults to
     *                 'SwatImagePreviewDisplay'.
     */
    protected function getJavaScriptClass()
    {
        return 'SwatImagePreviewDisplay';
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets inline JavaScript required by this image preview.
     *
     * @return string inline JavaScript needed by this widget.
     */
    protected function getInlineJavaScript()
    {
        static $shown = false;

        if (!$shown) {
            $javascript = $this->getInlineJavaScriptTranslations();
            $shown = true;
        } else {
            $javascript = '';
        }

        $javascript .= sprintf(
            "var %s = new %s(\n" . "%s, %s, %s, %s, %s, %s);\n",
            $this->id,
            $this->getJavaScriptClass(),
            SwatString::quoteJavaScriptString($this->id),
            SwatString::quoteJavaScriptString($this->preview_image),
            intval($this->preview_width),
            intval($this->preview_height),
            $this->show_title ? 'true' : 'false',
            SwatString::quoteJavaScriptString($this->preview_title),
        );

        if ($this->container_width !== null) {
            $javascript .= sprintf(
                '%s.width = %s;',
                $this->id,
                (int) $this->container_width,
            );
        }

        if ($this->container_height !== null) {
            $javascript .= sprintf(
                '%s.height = %s;',
                $this->id,
                (int) $this->container_height,
            );
        }

        return $javascript;
    }

    // }}}
    // {{{ protected function getInlineJavaScriptTranslations()

    /**
     * Gets translatable string resources for the JavaScript object for
     * this widget
     *
     * @return string translatable JavaScript string resources for this widget.
     */
    protected function getInlineJavaScriptTranslations()
    {
        $close_text =
            $this->close_text === null ? Swat::_('Close') : $this->close_text;

        return sprintf(
            "SwatImagePreviewDisplay.close_text = '%s';\n",
            $close_text,
        );
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this image display
     *
     * @return array the array of CSS classes that are applied to this image
     *                display.
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-image-preview-display'];
        $classes = array_merge($classes, parent::getCSSClassNames());
        return $classes;
    }

    // }}}
}
