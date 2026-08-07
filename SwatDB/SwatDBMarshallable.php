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
 * Note: the PHPStan-type should really be:
 *
 *     MarshallableArray array<int|string, string|MarshallableArray>
 *
 * But this causes a circular reference, which PHPStan does not like.  Instead,
 * we limit the enforcement to a depth of two levels, which is probably fine for
 * most cases.
 *
 * @phpstan-type MarshallableArray array<int|string, string|array<int|string, string|array>>
 *
 * @copyright 2013-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatDBMarshallable
{
    /**
     * Marshalls this object.
     *
     * ```php
     * $data = $object->marshall([
     *      'daughters',
     *      'sons' => [
     *          'grandkids'
     *      ]
     * ]);
     * ```
     *
     * @param MarshallableArray $tree optional. An array representing the data-structure
     *                                sub-tree to include in the marshalled data.
     *
     * @return MarshallableArray the marshalled data
     *
     * @throws SwatDBMarshallException if one of the sub-tree properties
     *                                 cannot be marshalled
     */
    public function marshall(array $tree = []): array;

    /**
     * Unmarshalls this object using the specified data.
     *
     * ```php
     * $object->unmarshall($data);
     * ```
     *
     * @param MarshallableArray $data optional. The marshalled object data.
     */
    public function unmarshall(array $data = []): void;
}
