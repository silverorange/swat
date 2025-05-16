<?php

/**
 * Stores and outputs an HTML head entry for a stylesheet include.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatStyleSheetHtmlHeadEntry extends SwatHtmlHeadEntry
{
    // {{{ protected function displayInternal()

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
            '<link rel="stylesheet" type="text/css" href="%s%s" />',
            $uri_prefix,
            $uri,
        );
    }

    // }}}
    // {{{ protected function displayInlineInternal()

    protected function displayInlineInternal($path)
    {
        echo '<style type="text/css" media="all">';
        readfile($path . $this->getUri());
        echo '</style>';
    }

    // }}}
}
