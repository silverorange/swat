<?php

/**
 * A single object to represent a database query range.
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
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBRange extends SwatObject
{
    /**
     * The limit of this range.
     *
     * @var int
     *
     * @see SwatDBRange::getLimit()
     */
    private $limit;

    /**
     * The offset of this range.
     *
     * @var int
     *
     * @see SwatDBRange::getOffset()
     */
    private $offset;

    /**
     * Creates a new database range.
     *
     * @param int $limit  the limit of this range
     * @param int $offset optional. The offset of this range. If not
     *                    specified, defaults to 0.
     */
    public function __construct($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = intval($offset);
    }

    /**
     * Gets the limit of this range.
     *
     * @return int the limit of this range
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets the offset of this range.
     *
     * @return int the offset of this range
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Increases the offset of this range.
     *
     * @param int $offset the amount by which to increase the offset
     */
    public function addOffset($offset)
    {
        $this->offset += intval($offset);
    }

    /**
     * Combines this range with another range forming a new range.
     *
     * Ranges are combined so the combined range includes both ranges. For
     * example, if a range of (10, 100) is combined with a
     * range of (20, 160) the resulting range will be (80, 100).
     *
     * <pre>
     * ..|====|................. range1
     * ...........|============| range2
     * ..|=====================| combined range
     * </pre>
     *
     * @param SwatDBRange $range the range to combine with this range
     *
     * @return SwatDBRange the combined range
     */
    public function combine(SwatDBRange $range)
    {
        // find leftmost extent
        $offset = min($this->getOffset(), $range->getOffset());

        if ($this->getLimit() === null || $range->getLimit() === null) {
            // rightmost extent is infinite
            $limit = null;
        } else {
            // find rightmost extent and convert to limit with known offset
            $this_limit = $this->getOffset() + $this->getLimit();
            $range_limit = $range->getOffset() + $range->getLimit();
            $limit = max($this_limit, $range_limit) - $offset;
        }

        return new SwatDBRange($limit, $offset);
    }
}
