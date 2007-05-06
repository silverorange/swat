<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/exceptions/SwatException.php';

/**
 * Thrown by {@link SwatForm} when a possible cross-site request forgery is
 * detected
 *
 * By design, it is not possible to get the correct authentication token from
 * this exception. Since it is not possible to get the correct authentication
 * token, the incorrect token is not useful and is also not availble in this
 * exception.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCrossSiteRequestForgeryException extends SwatException
{
}

?>
