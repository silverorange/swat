<?php

/**
 * Stores and outputs an HTML head entry for a JavaScript include
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatExternalJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
    // {{{ protected function displayInternal()

    protected function displayInternal($uri_prefix = '', $tag = null)
    {
        $uri = $this->uri;

        printf(
            '<script type="text/javascript" src="%s"></script>',
            $uri
        );
    }

    // }}}
    // {{{ protected function displayInlineInternal()

    protected function displayInlineInternal($path)
    {
        echo '<script type="text/javascript">';
        readfile($path . $this->getUri());
        echo '</script>';
    }

    // }}}
}
