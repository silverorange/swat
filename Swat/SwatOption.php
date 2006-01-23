<?php

require_once 'Swat/SwatObject.php';

/**
 * A simple class for storing {@link SwatFlydown} options
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatOption extends SwatObject
{
	/**
	 * Option title
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Optional content type for title
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * Option value
	 *
	 * @var mixed
	 */
	public $value = null;

	/**
	 * Creates a flydown option
	 *
	 * @param mixed $value Value of the option.
	 * @param string $title Displayed title of the option.
	 * @param string $content_type Optional content type of the title.
	 */
	public function __construct($value, $title, $content_type = 'text/plain')
	{
		$this->value = $value;
		$this->title = $title;
		$this->content_type = $content_type;
	}
}

?>
