<?php

require_once 'Swat/SwatObject.php';

/**
 * A single object to represent a database query range
 *
 * Objects of this class can be passed too and from methods as a single
 * argument representing the values of a limit/offset clause in a query. It is
 * intended that this object be used with MDB2 as follows:
 *
 * <code>
 * $range = $my_object->getRange(); // assigns a SwatDBRange
 * $db->setLimit($range->getLimit(), $range->getOffset());
 * </code>
 *
 * @package   SwatDB
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBRange extends SwatObject
{
	// {{{ private properties

	/**
	 * The limit of this range
	 *
	 * @var integer
	 *
	 * @see SwatDBRange::getLimit()
	 */
	private $limit;

	/**
	 * The offset of this range
	 *
	 * @var integer
	 *
	 * @see SwatDBRange::getOffset()
	 */
	private $offset;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new database range
	 *
	 * @param integer $limit the limit of this range.
	 * @param integer $offset optional. The offset of this range. If not
	 *                         specified, defaults to 0.
	 */
	public function __construct($limit, $offset = 0)
	{
		$this->limit = $limit;
		$this->offset = $offset;
	}

	// }}}
	// {{{ public function getLimit()

	/**
	 * Gets the limit of this range
	 *
	 * @return integer the limit of this range.
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	// }}}
	// {{{ public function getOffset()

	/**
	 * Gets the offset of this range
	 *
	 * @return integer the offset of this range.
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	// }}}
}

?>
