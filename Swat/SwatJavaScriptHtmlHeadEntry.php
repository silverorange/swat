<?php

/**
 * Stores and outputs an HTML head entry for a JavaScript include.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
    protected function displayInternal($uri_prefix = '', $tag = null)
    {
        $uri = $this->uri;

        // append tag if it is set
        if ($tag !== null) {
            $uri
                = mb_strpos($uri, '?') === false
                    ? $uri . '?' . $tag
                    : $uri . '&' . $tag;
        }

        printf(
            '<script type="text/javascript" src="%s%s"></script>',
            $uri_prefix,
            $uri,
        );
    }

    protected function displayInlineInternal($path)
    {
        echo '<script type="text/javascript">';
        readfile($path . $this->getUri());
        echo '</script>';
    }
}
