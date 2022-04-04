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
    // {{{ class constants

    const MODE_VISUAL = 1;
    const MODE_SOURCE = 2;

    // }}}
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
     * Editing mode to use
     *
     * Must be one of either {@link SwatTextareaEditor::MODE_VISUAL} or
     * {@link SwatTextareaEditor::MODE_SOURCE}.
     *
     * Defaults to <kbd>SwatTextareaEditor::MODE_VISUAL</kbd>.
     *
     * @var integer
     */
    public $mode = self::MODE_VISUAL;

    /**
     * Whether or not the mode switching behavior is enabled
     *
     * If set to false, only {@link SwatTextAreaEditor::$mode} will be ignored
     * and only {@link SwatTextAreaEditor::MODE_VISUAL} will be available for
     * the editor.
     *
     * @var boolean
     */
    public $modes_enabled = true;

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

        $this->addExternalJavaScript('https://cdn.tiny.cloud/1/mr7jywshgpa7hj8gp3njmyobnx8qz4ll5c7p115n3ls0knq4/tinymce/5/tinymce.min.js');
        $this->addJavaScript(
            'packages/swat/javascript/swat-z-index-manager.js'
        );
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

        // hidden field to preserve editing mode in form data
        $input_tag = new SwatHtmlTag('input');
        $input_tag->type = 'hidden';
        $input_tag->id = $this->id . '_mode';
        $input_tag->value = $this->mode;
        $input_tag->display();

        $div_tag->close();

        Swat::displayInlineJavaScript($this->getInlineJavaScript());
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
    // {{{ protected function getConfig()

    protected function getConfig()
    {
        $buttons = implode(' ', $this->getConfigButtons());

        $blockformats = array(
            'Paragraph=p',
            'Blockquote=blockquote',
            'Preformatted=pre',
            'Header 1=h1',
            'Header 2=h2',
            'Header 3=h3',
            'Header 4=h4',
            'Header 5=h5',
            'Header 6=h6'
        );

        $blockformats = implode('; ', $blockformats);

        $modes = $this->modes_enabled ? 'yes' : 'no';
        $image_server = $this->image_server ? $this->image_server : '';

        $config = array(
            'selector' => '#'.$this->id,
            'toolbar' => $buttons,
            'block_formats' => $blockformats, // https://www.tiny.cloud/docs/configure/editor-appearance/#block_formats
            'skin' => 'outside',
            'plugins' => 'code table lists media image link powerpaste',
            'convert_urls' => false,
            'paste_retain_style_properties' => 'background-color',
            'branding' => false,
            'powerpaste_word_import' => 'clean',
            'powerpaste_googledocs_import' => 'clean'
        );

        return $config;
    }

    // }}}
    // {{{ protected function getConfigButtons()

    protected function getConfigButtons()
    {
        return array(
            'bold',
            'italic',
            '|',
            'formatselect',
            '|',
            'removeformat',
            '|',
            'undo',
            'redo',
            '|',
            'outdent',
            'indent',
            '|',
            'bullist',
            'numlist',
            '|',
            'link',
            'image',
            'backcolor',
            'fontsizeselect'
        );
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    protected function getInlineJavaScript()
    {
        $base_href = 'editor_base_' . $this->id;
        ob_start();

        if ($this->base_href === null) {
            echo "var {$base_href} = " .
                "document.getElementsByTagName('base');\n";

            echo "if ({$base_href}.length) {\n";
            echo "\t{$base_href} = {$base_href}[0];\n";
            echo "\t{$base_href} = {$base_href}.href;\n";
            echo "} else {\n";
            echo "\t{$base_href} = location.href.split('#', 2)[0];\n";
            echo "\t{$base_href} = {$base_href}.split('?', 2)[0];\n";
            echo "\t{$base_href} = {$base_href}.substring(\n";
            echo "\t\t0, {$base_href}.lastIndexOf('/') + 1);\n";
            echo "}\n";
        } else {
            echo "var {$base_href} = " .
                SwatString::quoteJavaScriptString($this->base_href) .
                ";\n";
        }

        echo "tinyMCE.init({\n";

        $lines = array();
        foreach ($this->getConfig() as $name => $value) {
            if (is_string($value)) {
                $value = "'{$value}'";
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $lines[] = "\t" . $name . ": " . $value;
        }

        $lines[] = "\tdocument_base_url: {$base_href}";

        echo implode(",\n", $lines);

        // Make removeformat button also clear inline alignments, styles,
        // colors and classes.
        echo ",\n" .
            "\tformats: {\n" .
            "\t\tremoveformat : [\n" .
            "\t\t\t{\n" .
            "\t\t\t\tselector     : 'b,strong,em,i,font,u,strike',\n" .
            "\t\t\t\tremove       : 'all',\n" .
            "\t\t\t\tsplit        : true,\n" .
            "\t\t\t\texpand       : false,\n" .
            "\t\t\t\tblock_expand : true,\n" .
            "\t\t\t\tdeep         : true\n" .
            "\t\t\t},\n" .
            "\t\t\t{\n" .
            "\t\t\t\tselector     : 'span',\n" .
            "\t\t\t\tattributes   : [\n" .
            "\t\t\t\t\t'style',\n" .
            "\t\t\t\t\t'class',\n" .
            "\t\t\t\t\t'align',\n" .
            "\t\t\t\t\t'color',\n" .
            "\t\t\t\t\t'background'\n" .
            "\t\t\t\t],\n" .
            "\t\t\t\tremove       : 'empty',\n" .
            "\t\t\t\tsplit        : true,\n" .
            "\t\t\t\texpand       : false,\n" .
            "\t\t\t\tdeep         : true\n" .
            "\t\t\t},\n" .
            "\t\t\t{\n" .
            "\t\t\t\tselector     : '*',\n" .
            "\t\t\t\tattributes   : [\n" .
            "\t\t\t\t\t'style',\n" .
            "\t\t\t\t\t'class',\n" .
            "\t\t\t\t\t'align',\n" .
            "\t\t\t\t\t'color',\n" .
            "\t\t\t\t\t'background'\n" .
            "\t\t\t\t],\n" .
            "\t\t\t\tsplit        : false,\n" .
            "\t\t\t\texpand       : false,\n" .
            "\t\t\t\tdeep         : true\n" .
            "\t\t\t}\n" .
            "\t\t]\n" .
            "\t}";

        echo "\n});";

        return ob_get_clean();
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
