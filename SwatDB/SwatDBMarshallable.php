<?php

/**
 * Interface for marshalling and unmarshalling data-objects.
 *
 * Marshalling converts data-objects into primitive data types which can easily
 * and efficiently be serialized. Unmarshalling restores an object using
 * previously marshalled data.
 *
 * An advantage of marshalling over straight serialization is you can specify
 * exactly what tree of objects will be included in the marshalled result.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBMarshallable
{
    // {{{ public function marshall()

    /**
     * Marshalls this object.
     *
     * <code>
     * <?php
     * $data = $object->marshall(
     *     array(
     *         'daughters',
     *         'sons' => array(
     *             'grandkids'
     *         )
     *     )
     * );
     * ?>
     * </code>
     *
     * @param array $tree optional. An array representing the data-structure
     *                    sub-tree to include in the marshalled data.
     *
     * @return array the marshalled data
     *
     * @throws SwatDBMarshallException if one of the sub-tree properties
     *                                 cannot be marshalled
     */
    public function marshall(array $tree = []);

    // }}}
    // {{{ public function unmarshall()

    /**
     * Unmarshalls this object using the specified data.
     *
     * <code>
     * <?php
     * $object->unmarshall($data);
     * ?>
     * </code>
     *
     * @param array $data optional. The marshalled object data.
     */
    public function unmarshall(array $data = []);

    // }}}
}
