<?php

/**
 * A mapping of a data field to property of a cell renderer.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCellRendererMapping extends SwatObject
{

	/**
	 * The name of the property.
	 *
	 * @var string
	 */
	public $property;

	/**
	 * The name of the data field.
	 *
	 * @var string
	 */
	public $field;

	/**
	 * Whether the property is an array.
	 *
	 * @var boolean
	 */
	public $is_array = false;

	/**
	 * The array key if the property is an indexed array.
	 *
	 * @var mixed
	 */
	public $array_key = null;

	/**
	 * Create a new mapping object
	 *
	 * @param string $property the name of the property.
	 * @param string $field the name of the field.
	 */
	public function __construct($property, $field)
	{
		$this->property = $property;
		$this->field = $field;
	}

}

?>
