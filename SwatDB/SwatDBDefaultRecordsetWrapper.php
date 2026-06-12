<?php

/**
 * MDB2 Recordset Wrapper.
 *
 * Used to wrap an MDB2 recordset into a traversable collection of objects.
 *
 * @extends SwatDBRecordsetWrapper<stdClass>
 *
 * @copyright 2005-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDefaultRecordsetWrapper extends SwatDBRecordsetWrapper
{
    public function __construct(
        ?MDB2_Result_Common $rs = null
    ) {
        $this->row_wrapper_class = null;
        parent::__construct($rs);
    }
}
