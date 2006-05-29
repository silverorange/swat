<?php

require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * Stores and outputs an HTML head entry for a JavaScript include
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
	public function display($uri_prefix = '')
	{
		printf('<script type="text/javascript" src="%s%s"></script>',
			$uri_prefix,
			$this->uri);
	}
}

?>
