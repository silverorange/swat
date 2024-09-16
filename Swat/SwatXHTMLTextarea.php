<?php

/**
 * A text area that validates its content as an XHTML fragment against the
 * XHTML Strict DTD
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatXHTMLTextarea extends SwatTextarea
{


    /**
     * Whether or not to allow the user to ignore validation errors
     *
     * Setting this property to true will present a checkbox to the user
     * allowing the user to ignore validation errors generated by the XML
     * parser.
     *
     * @var boolean
     */
    public $allow_ignore_validation_errors = false;



    /**
     * Whether or not this XHTML entry has validation errors or not
     *
     * @var boolean
     */
    protected $has_validation_errors = false;

    /**
     * Composite checkbox control used to ignore XHTML validation errors
     *
     * @var SwatCheckbox
     */
    protected $ignore_errors_checkbox;



    public function process()
    {


        static $xhtml_template = '';

        if ($xhtml_template == '') {
            $doctype =
                '<!DOCTYPE html PUBLIC ' .
                '"-//W3C//DTD XHTML 1.0 Strict//EN" ' .
                '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' .
                "\n";

            $xhtml_template =
                $doctype .
                <<<EOB
                <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
                	<head>
                		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
                		<title>SwatXHTMLTextarea Content</title>
                	</head>
                	<body>
                		<div>
                		%s
                		</div>
                	</body>
                </html>

                EOB;
        }


        parent::process();

        $ignore_validation_errors =
            $this->allow_ignore_validation_errors &&
            $this->ignore_errors_checkbox->value;

        $xhtml_content = sprintf($xhtml_template, $this->getXHTMLContent());

        $errors = libxml_use_internal_errors(true);

        $document = new DOMDocument();
        $document->loadXML(
            $xhtml_content,
            LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID,
        );

        $xml_errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($errors);

        if (count($xml_errors) > 0 && !$ignore_validation_errors) {
            $this->addMessage($this->getValidationErrorMessage($xml_errors));
            $this->has_validation_errors = true;
        }
    }



    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        if (
            $this->allow_ignore_validation_errors &&
            ($this->has_validation_errors ||
                $this->ignore_errors_checkbox->value)
        ) {
            $ignore_field = $this->getCompositeWidget('ignore_field');
            $ignore_field->display();
        }
    }



    /**
     * Gets a human readable error message for XHTML validation errors on
     * this textarea's value
     *
     * @param array $xml_errors an array of LibXMLError objects.
     *
     * @return SwatMessage a human readable error message for XHTML validation
     *                      errors on this textarea's value.
     */
    protected function getValidationErrorMessage(array $xml_errors)
    {
        $ignored_errors = [
            'extra content at the end of the document',
            'premature end of data in tag html',
            'opening and ending tag mismatch between html and body',
            'opening and ending tag mismatch between body and html',
        ];

        $errors = [];
        foreach ($xml_errors as $error_object) {
            $error = $error_object->message;

            // further humanize
            $error = str_replace(
                'tag mismatch:',
                Swat::_('tag mismatch between'),
                $error,
            );

            // remove some stuff that only makes sense in document context
            $error = preg_replace('/\s?line:? [0-9]+\s?/ui', ' ', $error);
            $error = preg_replace('/in entity[:,.]?/ui', '', $error);
            $error = mb_strtolower($error);

            $error = str_replace(
                'xmlparseentityref: no name',
                Swat::_('unescaped ampersand. Use &amp;amp; instead of &amp;'),
                $error,
            );

            $error = str_replace(
                'starttag: invalid element name',
                Swat::_('unescaped less-than. Use &amp;lt; instead of &lt;'),
                $error,
            );

            $error = str_replace(
                'specification mandate value for attribute',
                Swat::_('a value is required for the attribute'),
                $error,
            );

            $error = preg_replace(
                '/^no declaration for attribute (.*?) of element (.*?)$/',
                Swat::_('the attribute \1 is not valid for the element \2'),
                $error,
            );

            $error = str_replace(
                'attvalue: " or \' expected',
                Swat::_(
                    'attribute values must be contained within quotation ' .
                        'marks',
                ),
                $error,
            );

            $error = trim($error);

            if (!in_array($error, $ignored_errors)) {
                $errors[] = $error;
            }
        }

        $content = Swat::_('%s must be valid XHTML markup: ');
        $content .= '<ul><li>' . implode(',</li><li>', $errors) . '.</li></ul>';
        $message = new SwatMessage($content, 'error');
        $message->content_type = 'text/xml';

        return $message;
    }



    /**
     * Creates the composite checkbox used by this XHTML textarea
     *
     * @see SwatWidget::createCompositeWidgets()
     */
    protected function createCompositeWidgets()
    {
        $this->ignore_errors_checkbox = new SwatCheckbox(
            $this->id . '_ignore_checkbox',
        );

        $ignore_field = new SwatFormField($this->id . '_ignore_field');
        $ignore_field->title = Swat::_('Ignore XHTML validation errors');
        $ignore_field->add($this->ignore_errors_checkbox);

        $this->addCompositeWidget($ignore_field, 'ignore_field');
    }



    protected function getXHTMLContent()
    {
        return $this->value;
    }

}
