<?php

require_once 'SwatDB/SwatDBDefaultDataObject.php';
require_once 'SwatDB/SwatDBRecordsetWrapper.php';

/**
 * MDB2 Recordset Wrapper
 *
 * Used to wrap an MDB2 recordset into a traversable collection of objects.
 *
 * @package   SwatDB
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBDefaultRecordsetWrapper extends SwatDBRecordsetWrapper
{
    public function __construct($rs)
    {
        $this->row_wrapper_class = 'SwatDBDefaultDataObject';
        parent::__construct($rs);
    }
}

?>
