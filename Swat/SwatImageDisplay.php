<?php

/**
 * Image display control.
 *
 * This control simply displays a static image.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageDisplay extends SwatControl
{
    // {{{ public properties

    /**
     * Image.
     *
     * The src attribute in the XHTML img tag.
     *
     * @var string
     */
    public $image;

    /**
     * Optional array of values to substitute into the image property.
     *
     * Uses vsprintf() syntax, for example:
     *
     * <code>
     * $my_image->image = 'mydir/%s.%s';
     * $my_image->values = array('myfilename', 'ext');
     * </code>
     *
     * @var array
     */
    public $values = [];

    /**
     * Image height.
     *
     * The height attribute in the XHTML img tag.
     *
     * @var int
     */
    public $height;

    /**
     * Image width.
     *
     * The width attribute in the XHTML img tag.
     *
     * @var int
     */
    public $width;

    /**
     * The total height that the image occupies.
     *
     * Extra margin will be adding to the style of the img tag if the height is
     * less than occupy_height.
     *
     * @var int
     */
    public $occupy_height;

    /**
     * The total width that the image occupies.
     *
     * Extra margin will be adding to the style of the img tag if the width is
     * less than occupy_width.
     *
     * @var int
     */
    public $occupy_width;

    /**
     * Image title.
     *
     * The title attribute in the XHTML img tag.
     *
     * @var string
     */
    public $title;

    /**
     * Image alt text.
     *
     * The alt attribute in the XHTML img tag.
     *
     * @var string
     */
    public $alt;

    // }}}
    // {{{ public function display()

    /**
     * Displays this image.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $image_tag = new SwatHtmlTag('img');
        $image_tag->id = $this->id;
        $image_tag->class = $this->getCSSClassString();

        if (count($this->values)) {
            $image_tag->src = vsprintf($this->image, $this->values);
        } else {
            $image_tag->src = $this->image;
        }

        if ($this->height !== null) {
            $image_tag->height = $this->height;
        }

        if ($this->width !== null) {
            $image_tag->width = $this->width;
        }

        $image_tag->style = self::getOccupyMargin(
            $this->width,
            $this->height,
            $this->occupy_width,
            $this->occupy_height,
        );

        if ($this->title !== null) {
            $image_tag->title = $this->title;
        }

        // alt is a required XHTML attribute. We should always display it even
        // if it is not specified.
        $image_tag->alt = $this->alt === null ? '' : $this->alt;

        $image_tag->display();
    }

    // }}}
    // {{{ public static function getOccupyMargin()

    public static function getOccupyMargin(
        $width,
        $height,
        $occupy_width = null,
        $occupy_height = null,
    ) {
        $margin_x = 0;
        $margin_y = 0;

        if ($occupy_width !== null && $occupy_width > $width) {
            $margin_x = intval($occupy_width - $width);
        }

        if ($occupy_height !== null && $occupy_height > $height) {
            $margin_y = intval($occupy_height - $height);
        }

        if ($margin_x > 0 || $margin_y > 0) {
            $style = sprintf(
                $margin_x % 2 === 0 && $margin_y % 2 === 0
                    ? 'margin: %dpx %dpx'
                    : 'margin: %dpx %dpx %dpx %dpx;',
                floor(((float) $margin_y) / 2),
                ceil(((float) $margin_x) / 2),
                ceil(((float) $margin_y) / 2),
                floor(((float) $margin_x) / 2),
            );
        } else {
            $style = null;
        }

        return $style;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this image display.
     *
     * @return array the array of CSS classes that are applied to this image
     *               display
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-image-display'];

        return array_merge($classes, parent::getCSSClassNames());
    }

    // }}}
}
