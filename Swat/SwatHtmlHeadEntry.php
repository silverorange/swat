<?php

require_once 'Swat/SwatObject.php';

/**
 * Stores and outputs an HTML head entry
 *
 * Head entries are things like scripts and styles that belong in the HTML
 * head section.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatHtmlHeadEntry extends SwatObject
{
	/**
	 * A stylesheet head entry
	 */
	const TYPE_STYLE = 0;

	/**
	 * A JavaScript head entry
	 */
	const TYPE_JAVASCRIPT = 1;
	
	/**
	 * The type of this head entry
	 *
	 * This should be a valid SwatHtmlHeadEntry::TYPE_* constant.
	 *
	 * @var integer
	 */
	public $type = self::TYPE_STYLE;

	/**
	 * The uri of this head entry
	 *
	 * @var string
	 */
	public $uri = ''

	/**
	 * Displays this html head entry
	 *
	 * Entries are displayed differently based on type.
	 */
	public function display()
	{
		switch ($this->type) {
		case self::TYPE_STYLE:
			echo '<style type="text/css" media="all">',
				'@import "', $this->uri, '";',
				"</style>\n";

			break;
		case self::TYPE_JAVASCRIPT:
			echo '<script type="text/javascript" src="', $this->uri, '">',
				"</script>\n";
				
			break;
		case default:
		}
	}
}

?>
