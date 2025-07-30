<?php

/**
 * Stores and outputs an HTML head entry for an external JavaScript resource.
 *
 * @copyright 2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatExternalJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
    protected function displayInternal($uri_prefix = '', $tag = null)
    {
        $uri = $this->uri;

        printf('<script type="text/javascript" src="%s"></script>', $uri);
    }

    protected function displayInlineInternal($path)
    {
        // Can't inline external JavaScript resources
    }
}
