<?php

/**
 * Thrown when a object is not found
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatObjectNotFoundException extends SwatException
{

	/**
	 * The object id that was searched for
	 *
	 * @var string
	 */
	protected $object_id = null;

	/**
	 * Creates a new object not found exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param string $object_id the object id that was searched for.
	 */
	public function __construct($message = null, $code = 0, $object_id = null)
	{
		parent::__construct($message, $code);
		$this->object_id = $object_id;
	}

	/**
	 * Gets the object id that was searched for
	 *
	 * @return string the object id that was searched for.
	 */
	public function getObjectId()
	{
		return $this->object_id;
	}

}

?>
