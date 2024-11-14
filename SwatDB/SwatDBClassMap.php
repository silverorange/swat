<?php

/**
 * Maps class names to overridden class names
 *
 * This is a static class and should not be instantiated.
 *
 * @package   SwatDB
 * @copyright 2006-2024 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @template TParent of object
 * @template TChild of TParent
 */
class SwatDBClassMap
{
    /**
     * An associative array of class-mappings
     *
     * The array has keys of the 'from class name' and values of the
     * 'to class name'.
     *
     * @var array<class-string<TParent>, class-string<TChild>>
     */
    private static array $map = [];

    /**
     * Maps a class name to another class name
     *
     * Subsequent calls to {@link SwatDBClassMap::get()} using the
     * <i>$from_class_name</i> will return the <i>$to_class_name</i>. Class
     * names may be mapped to already mapped class names. For example:
     *
     * <code>
     * SwatDBClassMap::add('Object', 'MyObject');
     * SwatDBClassMap::add('MyObject', 'MyOtherObject');
     * echo SwatDBClassMap::get('Object'); // MyOtherObject
     * </code>
     *
     * If a circular dependency is created, an exception is thrown. If the
     * <i>$from_class_name</i> is already mapped to another class the old
     * mapping is overwritten.
     *
     * @param class-string<TParent> $from_class_name the class name to map from.
     * @param class-string<TChild> $to_class_name the class name to map to.
     *                                             The mapped class must be a
     *                                             subclass of the
     *                                             <i>$from_class_name</i> otherwise
     *                                             class resolution using
     *                                             {@link SwatDBClassMap::get()}
     *                                             will throw an exception.
     *
     * @throws SwatInvalidClassException if <i>$to_class_name</i> is not a subclass
     * *                                   of <i>$from_class_name</i>.
     * @throws SwatException if the added mapping creates a circular dependency.
     */
    public static function add(
        string $from_class_name,
        string $to_class_name,
    ): void
    {
        // check for subclass
        if (!is_subclass_of($to_class_name, $from_class_name)) {
            throw new SwatInvalidClassException(
                sprintf(
                    'Invalid ' .
                    'class-mapping detected. %s is not a subclass of %s.',
                    $to_class_name,
                    $from_class_name,
                ),
            );
        }

        // check for circular dependency
        if (array_key_exists($to_class_name, self::$map)) {
            $class_name = $to_class_name;
            $child_class_names = [$class_name];
            while (array_key_exists($class_name, self::$map)) {
                $class_name = self::$map[$class_name];
                $child_class_names[] = $class_name;
            }

            if (in_array($from_class_name, $child_class_names)) {
                throw new SwatException(
                    sprintf(
                        'Circular class dependency detected: %s => %s',
                        $from_class_name,
                        implode(' => ', $child_class_names),
                    ),
                );
            }
        }

        self::$map[$from_class_name] = $to_class_name;
    }

    /**
     * Resolves a class name from the class map
     *
     * @param class-string<TParent> $from_class_name the name of the class to resolve.
     *
     * @return class-string<TChild> the resolved class name. If no class mapping
     *                              exists for the given class name, the same
     *                              class name is returned.
     */
    public static function get(string $from_class_name): string
    {
        $to_class_name = $from_class_name;

        while (array_key_exists($from_class_name, self::$map)) {
            $to_class_name = self::$map[$from_class_name];
            $from_class_name = $to_class_name;
        }

        return $to_class_name;
    }

    /**
     * Resolves a class name from the class map and returns a new instance of
     * that class.
     *
     * @param class-string<TParent> $from_class_name the name of the class to resolve
     * @param mixed ...$params extra parameters to pass to the new class constructor
     *
     * @return object<TChild> a new instance of the resolved class
     */
    public static function new(
        string $from_class_name,
        mixed  ...$params,
    ): object
    {
        $class = self::get($from_class_name);

        return new $class(...$params);
    }

    /**
     * The class map is a static object and should not be instantiated
     */
    private function __construct() {}
}
