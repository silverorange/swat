<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * Container for package wide static methods
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Swat
{
	// {{{ constants

	/**
	 * The gettext domain for Swat
	 *
	 * This is used to support multiple locales.
	 */
	const GETTEXT_DOMAIN = 'swat';

	// }}}
	// {{{ public static function _()

	/**
	 * Translates a phrase
	 *
	 * This is an alias for {@link Swat::gettext()}.
	 *
	 * @param string $message the phrase to be translated.
	 *
	 * @return string the translated phrase.
	 */
	public static function _($message)
	{
		return self::gettext($message);
	}

	// }}}
	// {{{ public static function gettext()

	/**
	 * Translates a phrase
	 *
	 * This method relies on the php gettext extension and uses dgettext()
	 * internally.
	 *
	 * @param string $message the phrase to be translated.
	 *
	 * @return string the translated phrase.
	 */
	public static function gettext($message)
	{
		return dgettext(self::GETTEXT_DOMAIN, $message);
	}

	// }}}
	// {{{ public static function ngettext()

	/**
	 * Translates a plural phrase
	 *
	 * This method should be used when a phrase depends on a number. For
	 * example, use ngettext when translating a dynamic phrase like:
	 *
	 * - "There is 1 new item" for 1 item and
	 * - "There are 2 new items" for 2 or more items.
	 *
	 * This method relies on the php gettext extension and uses dngettext()
	 * internally.
	 *
	 * @param string $singular_message the message to use when the number the
	 *                                  phrase depends on is one.
	 * @param string $plural_message the message to use when the number the
	 *                                phrase depends on is more than one.
	 * @param integer $number the number the phrase depends on.
	 *
	 * @return string the translated phrase.
	 */
	public static function ngettext($singular_message, $plural_message, $number)
	{
		return dngettext(self::GETTEXT_DOMAIN,
			$singular_message, $plural_message, $number);
	}

	// }}}
	// {{{ public static function setupGettext()

	public static function setupGettext()
	{
		$path = '@DATA-DIR@/Swat/locale';
		if (mb_substr($path, 0, 1) === '@') {
			$path = __DIR__.'/../locale';
		}

		bindtextdomain(self::GETTEXT_DOMAIN, $path);
		bind_textdomain_codeset(self::GETTEXT_DOMAIN, 'UTF-8');
	}

	// }}}
	// {{{ public static function displayMethods()

	/**
	 * Displays the methods of an object
	 *
	 * This is useful for debugging.
	 *
	 * @param mixed $object the object whose methods are to be displayed.
	 */
	public static function displayMethods($object)
	{
		echo sprintf(self::_('Methods for class %s:'), get_class($object));
		echo '<ul>';

		foreach (get_class_methods(get_class($object)) as $method_name)
			echo '<li>', $method_name, '</li>';

		echo '</ul>';
	}

	// }}}
	// {{{ public static function displayProperties()

	/**
	 * Displays the properties of an object
	 *
	 * This is useful for debugging.
	 *
	 * @param mixed $object the object whose properties are to be displayed.
	 */
	public static function displayProperties($object)
	{
		$class = get_class($object);

		echo sprintf(self::_('Properties for class %s:'), $class);
		echo '<ul>';

		foreach (get_class_vars($class) as $property_name => $value) {
			$instance_value = $object->$property_name;
			echo '<li>', $property_name, ' = ', $instance_value, '</li>';
		}

		echo '</ul>';
	}

	// }}}
	// {{{ public static function printObject()

	/**
	 * Displays an object's properties and values recursively
	 *
	 * Note:
	 *
	 * If the object being printed is a UI object then its parent property
	 * is temporarily set to null to prevent recursing up the widget tree.
	 *
	 * @param mixed $object the object to display.
	 */
	public static function printObject($object)
	{
		echo '<pre>'.print_r($object, true).'</pre>';
	}

	// }}}
	// {{{ public static function displayInlineJavaScript()

	/**
	 * Displays inline JavaScript properly encapsulating the script in a CDATA
	 * section
	 *
	 * @param string $javascript the inline JavaScript to display.
	 */
	public static function displayInlineJavaScript($javascript)
	{
		if ($javascript != '') {
			echo '<script type="text/javascript">', "\n//<![CDATA[\n",
				rtrim($javascript),
				"\n//]]>\n</script>";
		}
	}

	// }}}
	// {{{ private function __construct()

	/**
	 * Don't allow instantiation of the Swat object
	 *
	 * This class contains only static methods and should not be instantiated.
	 */
	private function __construct()
	{
	}

	// }}}
}

// {{{ dummy dngettext()

/*
 * Define a dummy dngettext() for when gettext is not available.
 */
if (!function_exists("dngettext")) {

	/**
	 * Dummy translation function performs a passthrough on string to be
	 * translated
	 *
	 * This function is for compatibility with PHP installations not using
	 * gettext.
	 *
	 * @param string $domain the translation domain. Ignored.
	 * @param string $messageid1 the singular form.
	 * @param string $messageid2 the plural form.
	 * @Param integer $n the number.
	 *
	 * @return string <i>$messageid1</i> id <i>$n</i> is one, otherwise
	 *                <i>$messageid2</i>.
	 */
	function dngettext($domain, $messageid1, $messageid2, $n)
	{
		if ($n == 1)
			return $messageid1;

		return $messageid2;
	}

}

// }}}
// {{{ dummy dgettext()

/*
 * Define a dummy dgettext() for when gettext is not available.
 */
if (!function_exists("dgettext")) {

	/**
	 * Dummy translation function performs a passthrough on string to be
	 * translated
	 *
	 * This function is for compatibility with PHP installations not using
	 * gettext.
	 *
	 * @param string $domain the translation domain. Ignored.
	 * @param string $messageid the string to be translated.
	 *
	 * @return string <i>$messageid</i>.
	 */
	function dgettext($domain, $messageid)
	{
		return $messageid;
	}

}

// }}}

Swat::setupGettext();

?>
