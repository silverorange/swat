<?php

/**
 * A class representing a divider in a flydown
 *
 * This class is for semantic purposed only. The flydown handles all the
 * displaying of dividers and regular flydown options.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFlydownDivider extends SwatOption
{
	// {{{ public function __construct()

	/**
	 * Creates a flydown option
	 *
	 * @param mixed $value value of the option. This defaults to null.
	 * @param string $title displayed title of the divider. This defaults to
	 *                       two em dashes.
	 * @param string $content_type optional. The content type of the divider. If
	 *                              not specified, defaults to 'text/plain'.
	 */
	public function __construct($value = null, $title = null,
		$content_type = 'text/plain')
	{
		if ($title === null)
			$title = str_repeat('—', 6);

		parent::__construct($value, $title, $content_type);
	}

	// }}}
}

?>
