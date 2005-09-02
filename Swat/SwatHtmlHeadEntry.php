<?php

class SwatHtmlHeadEntry extends SwatObject
{
	const TYPE_STYLE = 0;
	const TYPE_JAVASCRIPT = 1;
	
	public $type = self::TYPE_STYLE;
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
			break;
		case self::TYPE_JAVASCRIPT:
			break;
		case default:
		}
	}
}

?>
