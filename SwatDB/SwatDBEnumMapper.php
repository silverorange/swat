<?php

/**
 * Class to help map database enum columns to PHP enum classes.
 *
 * Once a connection to the database has been created, use the
 * SwatDBEnumMapper::initialize() method to set up mapping of database enum
 * column types to PHP enum classes. This should happen once, as early
 * as possible in your application.
 *
 * Example code:
 *
 *     $db = SwatDB::connect('...dsn...');
 *     $map = [
 *         'status_type' => MyStatusEnum::class
 *     ];
 *     SwatDBEnumMapper::initialize($db, $map);
 *
 * From here on, any queries against tables with columns of the `status_type` type
 * will map those columns to MyStatusEnum classes.
 *
 * You should also quote these values using the "enum" type when using them in queries:
 *
 *     $status = MyStatusEnum::IN_PROGRESS;
 *     $sql = sprintf(
 *         'UPDATE table SET status = '%s' WHERE ...',
 *         $db->quote($status, 'enum')
 *     );
 *     $db->query($sql);
 *
 * Enum classes can be simple unit enums (in which case the enum case _names_
 * should exactly match the possible values coming from the database), or they
 * can be backed enums (in which case the enum case _values_ should exactly match).
 *
 * @copyright 2025 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @template TEnumClass of \UnitEnum
 * @template TEnumClassString of class-string<TEnumClass>
 * @template TEnumMap of array<string, TEnumClassString>
 */
class SwatDBEnumMapper
{
    /**
     * The map of database column types to PHP enum classes.
     *
     * @var TEnumMap
     */
    protected static array $map = [];

    /**
     * Load the mapping into the static class.
     *
     * @param TEnumMap $map
     */
    public static function setMap(array $map): static
    {
        self::$map = $map;

        return new static();
    }

    /**
     * Set up the database drive to automagically handle the enum mapping.
     *
     * @param TEnumMap $map
     */
    public static function initialize(MDB2_Driver_Common &$db, array $map): void
    {
        self::$map = $map;

        // set up the callback mapping
        $db->loadModule('Datatype', null, true);

        // map the DB column types to custom MDB2 types (using the PHP class name)
        $datatype_map = [
            ...$map,
            // add one more entry to make db->quote()ing enum values easier
            'enum' => 'enum',
        ];

        // tell MDB2 to use the EnumMapper::handle() method to do the enum conversion
        $datatype_map_callback = array_fill_keys(
            array_values($datatype_map),
            self::handle(...),
        );

        // tells MDB2 to treat DB enum columns as their own type
        $nativetype_map_callback = array_fill_keys(
            array_keys($map),
            fn (MDB2_Driver_Common $db, array $field) => [
                [$field['type']],
                null,
                null,
                false,
            ],
        );

        // set the options on the database
        $db->setOption('datatype_map', $datatype_map);
        $db->setOption('datatype_map_callback', $datatype_map_callback);
        $db->setOption('nativetype_map_callback', $nativetype_map_callback);
    }

    /**
     * Main entry point used by MDB2 to manage custom field types and data mapping.
     *
     * @return ($method is 'convertResult'
     *      ? TEnumClass
     *      : ($method is 'compareDefinition' ? array : string)
     *  )
     *
     * @throws SwatDBException
     */
    public static function handle(
        MDB2_Driver_Common $db,
        string $method,
        array $parameters,
    ): array|string|UnitEnum {
        return match ($method) {
            'quote'         => self::quote($db, $parameters['value']),
            'convertResult' => self::convertResult(
                $parameters['type'],
                $parameters['value'],
            ),
            'getDeclaration' => $db->datatype->getDeclaration(
                'text',
                $parameters['name'],
                $parameters['field'],
            ),
            'mapPrepareDatatype' => $db->datatype->mapPrepareDatatype('text'),
            'compareDefinition'  => self::compareDefinition($db, $parameters),
            'getValidTypes'      => '',
            default              => throw new SwatDBException(
                "EnumMapper::handle() does not support method: {$method}",
            ),
        };
    }

    /**
     * Converts an enum to a string value for storing in the database.
     *
     * Backed enums use the `->value` property; unit enums use the `->name` property.
     * The resulting string is quoted normally.
     *
     * @param TEnumClass $value
     */
    protected static function quote(
        MDB2_Driver_Common $db,
        UnitEnum $value,
    ): string {
        $string = $value instanceof BackedEnum ? $value->value : $value->name;

        return $db->datatype->quote($string, 'text');
    }

    /**
     * Converts a string value from the database into a PHP enum class of the given type.
     *
     * For backed enums, the `::from()` method is used.  For unit enums, the string value
     * is assumed to be the actual enum case name.
     *
     * @param TEnumClassString $classname
     *
     * @return TEnumClass
     *
     * @throws SwatDBException
     */
    protected static function convertResult(
        string $classname,
        mixed $value,
    ): UnitEnum {
        try {
            $reflection = new ReflectionEnum($classname);
        } catch (ReflectionException $e) {
            throw new SwatDBException(
                sprintf('"%s" does not appear to be a PHP enum', $classname),
            );
        }

        // handle backed enums
        if ($reflection->isBacked()) {
            try {
                return $classname::from($value);
            } catch (ValueError $e) {
                throw new SwatDBException(
                    sprintf(
                        'Can not create a backed enum instance of "%s" from the value "%s"',
                        $classname,
                        $value,
                    ),
                );
            }
        }

        // handle unit enums
        try {
            return $reflection->getCase($value)->getValue();
        } catch (ReflectionException $e) {
            throw new SwatDBException(
                sprintf(
                    'Can not create a unit enum instance of "%s" from the value "%s"',
                    $classname,
                    $value,
                ),
            );
        }
    }

    /**
     * @see MDB2_Driver_Datatype_Common::compareDefinition
     */
    protected static function compareDefinition(
        MDB2_Driver_Common $db,
        array $parameters,
    ): array {
        $parameters['current']['type'] = 'text';

        if ($parameters['previous']['type'] === 'enum') {
            $parameters['previous']['type'] = 'text';
        }

        return $db->datatype->compareDefinition(
            $parameters['current'],
            $parameters['previous'],
        );
    }

    /**
     * Static class should not be instantiated.
     */
    final private function __construct() {}
}
