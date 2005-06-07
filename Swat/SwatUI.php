<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatContainer.php';

/**
 * Generates a Swat widget tree from an XML UI file
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatUI extends SwatObject
{
	/**
	 * An array that maps other package classes to filenames
	 *
	 * The array is of the form:
	 *    prefix => path
	 * Where prefix is the classname prefix and path is the relative path where
	 * the source file may be included from.
	 *
	 * @var array
	 */
	protected $classmap = array();

	/**
	 * An array of widgets populated when a UI file is parsed
	 *
	 * This array is used internally. The array is of the form:
	 *    id => object reference
	 * Where id is the unique identifier of the widget.
	 *
	 * @var array
	 */
	private $widgets = array();

	/**
	 * The root container of the widget tree created by this UI
	 *
	 * @var SwatContainer
	 */
	private $root = null;

	/**
	 * Creates a new UI
	 *
	 * @param SwatContainer $container an optional reference to a container
	 *                                  object that will be the root element of
	 *                                  the widget tree.
	 */
	public function __construct($container = null)
	{
		if ($container !== null && $container instanceof SwatContainer)
			$this->root = $container;
		else
			$this->root = new SwatContainer();
	}

	/**
	 * Loads a UI from an XML file
	 *
	 * @param string $filename the filename of the XML UI file to load.
	 *
	 * @throws SwatException
	 */
	public function loadFromXML($filename)
	{
		$xmlfile = null;

		if (file_exists($filename)) {
			$xmlfile = $filename;
		} else {
			$paths = explode(':', ini_get('include_path'));

			foreach ($paths as $path) {
				if (file_exists($path.'/'.$filename)) {
					$xmlfile = $path.'/'.$filename;
					break;
				}
			}
		}

		if ($xmlfile === null)
			throw new SwatException(sprintf(__CLASS__.
				': XML file not found: %s', $filename));

		$xml = simplexml_load_file($xmlfile);

		$this->parseUI($xml, $this->root);
	}

	/**
	 * Retrieves a widget from the internal widget list
	 *
	 * Looks up a widget in the widget list by the widget's unique identifier.
	 *
	 * @param string $id the id of the widget to retrieve.
	 * @param boolean $silent if true, return null instead of throwing an 
	 *                         exception if the widget is not found.
	 *
	 * @return SwatWidget a reference to the widget.
	 *
	 * @throws SwatException
	 */
	public function getWidget($id, $silent = false)
	{
		if (array_key_exists($id, $this->widgets))
			return $this->widgets[$id];
		else
			if ($silent)
				return null;
			else
				throw new SwatException(__CLASS__.
					": no widget with an id of '{$id}'");
	}

	/**
	 * Retrieves the topmost widget
	 *
	 * Looks up the widget at the root of the widget tree. The widget is
	 * always a container.
	 *
	 * @return SwatContainer a reference to the container widget.
	 */
	public function getRoot()
	{
		return $this->root;
	}

	/**
	 * Recursivly parses an XML node into a widget tree
	 *
	 * Calls self on all node children.
	 *
	 * @param Object $node the XML node to begin with.
	 * @param SwatContainer $parent_widget the parent widget to add parsed
	 *                                      objects to.
	 */
	private function parseUI($node, $parent_widget)
	{
		foreach ($node->children() as $child_name => $child_node) {
			if ($child_name == 'property') {
				$this->parseProperty($child_node, $parent_widget);
			} else {
				$parsed_object = $this->parseNode($child_node);

				$this->checkParsedObject($parsed_object, $child_name);

				/*
				 * No exceptions were thrown and the widget has an id
				 * so add to widget list to make it look-up-able.
				 */
				if ($child_name == 'widget' && $parsed_object->id !== null) {
					$this->widgets[$parsed_object->id] = $parsed_object;
				}
				
				$this->attachToParent($parsed_object, $parent_widget);
				$this->parseUI($child_node, $parsed_object);
			}
		}
	}

	/**
	 * Does some error checking on a parsed object
	 *
	 * Checks to make sure widget objects are created from widget elements
	 * and other objects are created from object elements.
	 *
	 * @param SwatObject $parsed_object an object that has been parsed from XML.
	 * @param string $element_name the name of the XML element node the object
	 *                              was parsed from.
	 *
	 * @throws SwatException
	 */
	private function checkParsedObject($parsed_object, $element_name)
	{
		if ($element_name == 'widget') {
			if (class_exists('SwatWidget') &&
				$parsed_object instanceof SwatWidget &&
				$parsed_object->id !== null) {

				// make sure id is unique
				if (isset($this->widgets[$parsed_object->id]))
					throw new SwatException(__CLASS__.
						": widget with an id of '{$parsed_object->id}' ".
						"already exists.");

			} elseif (!class_exists('SwatWidget') ||
				!$parsed_object instanceof SwatWidget) {
				
				throw new SwatException(__CLASS__.
					': '.get_class($parsed_object).' is declared as a widget '.
					'but is not an instance of SwatWidget.');
			}
		} elseif ($childname == 'object') {
			if (class_exists('SwatWidget') &&
				$parsed_object instanceof SwatWidget) {
				
				throw new SwatException(__CLASS__.
					': '.get_class($parsed_object).' is declared as an object '.
					'but is an instance of SwatWidget and should be '.
					'declared as a widget.');
			}
		}
	}

	/**
	 * Attaches a widget to a parent widget in the widget tree
	 *
	 * @param SwatWidget $wiget the widget to attach.
	 * @param SwatUIParent $parent the parent to attach the widget to.
	 *
	 * @throws SwatException
	 */
	private function attachToParent($widget, $parent)
	{
		if ($parent instanceof SwatUIParent)
			$parent->addChild($widget);
		else
			throw new SwatException(__CLASS__.
				': '.get_class($parent).' does not implement SwatUIParent.');

	}

	/**
	 * Parses a single XML node into a PHP object
	 *
	 * @param array $node the XML node data to parse.
	 *
	 * @return a reference to the object created.
	 */
	private function parseNode($node)
	{
		if (isset($node['class']))
			$class = (string)$node['class'];
		else
			throw new SwatException(__CLASS__.
				": widget or object element is missing 'class' attribute.");
	
		$classfile = "Swat/{$class}.php";

		if (count($this->classmap)) {
			foreach ($this->classmap as $prefix => $path) {
				if (strncmp($class, $prefix, strlen($prefix)) == 0)
					$classfile = "{$path}/{$class}.php";
			}
		}

		require_once $classfile;
		$node_class = new $class();
	
		if (isset($node['id']))
			$node_class->id = (string)$node['id'];

		return $node_class;
	}

	/**
	 * Parses a single XML property node and applies it to an object
	 *
	 * @param array $property_node the XML property node data to parse.
	 * @param SwatObject $object the object to apply the property to.
	 *
	 * @throws SwatException
	 */
	private function parseProperty($property_node, $object)
	{
		$class_properties = get_class_vars(get_class($object));
		
		if (!isset($property_node['name'])) {
			throw new SwatException(sprintf(__CLASS__.
				": property element missing 'name' attribute for class %s",
				get_class($object)));
		
		} elseif (!isset($property_node['value'])) {
			throw new SwatException(sprintf(__CLASS__.
				": property element missing 'value' attribute for class %s",
				get_class($object)));
		
		} elseif (!array_key_exists((string)$property_node['name'],
			$class_properties)) {
			
			throw new SwatException(sprintf(__CLASS__.
				": no property named '%s' in class %s",
				(string)$property_node['name'], get_class($object)));
				
		} else {
			$name = (string)$property_node['name'];
			$value = (string)$property_node['value'];
			$translatable = (isset($property_node['translatable']) &&
				strtolower((string)$property_node['translatable']) == 'yes');

			$type = (isset($property_node['type'])) ?
				(string)$property_node['type'] : null;
			
			$object->$name =
				$this->parseValue($name, $value, $type, $translatable, $object);
		}
	}

	/**
	 * Further parses a property
	 *
	 * Also does error notification in the event of a missing or unknown type
	 * attribute.
	 *
	 * @param string $name the name of the property.
	 * @param string $value the value of the property.
	 * @param string $type the type of the value.
	 * @param boolean translatable whether the property is translatable.
	 * @param SwatObject $object the object the property applies to.
	 *
	 * @return mixed the value of the property as an appropriate PHP datatype.
	 */
	private function parseValue($name, $value, $type, $translatable, $object)
	{
		switch ($type) {
		case 'boolean':
			return ($value == 'true') ? true : false;
		case 'integer':
			return intval($value);
		case 'float':
			return floatval($value);
		case 'string':
			return $this->translateValue($value, $translatable);
		case 'data':
			$object->parent->linkField($object, $value, $name);
			return null;
		default:
			if ($value == 'false' || $value == 'true' )
				trigger_error(__CLASS__.': Possible missing type="boolean" '.
					'attribute on property element', E_USER_NOTICE);

			if (is_numeric($value))
				trigger_error(__CLASS__.': Possible missing type="integer" '.
					' or type="float" attribute on property element',
					E_USER_NOTICE);

			// default to returnsing a string
			return $this->translateValue($value, $translatable);
		}
	}

	/**
	 * Translates a property value if possible
	 *
	 * @param string $value the value to be translated.
	 * @param boolean $translatable whether or not it is possible to translate
	 *                               the value.
	 *
	 * @return string the translated value if possible, otherwise $value.
	 */
	private function translateValue($value, $translatable)
	{
		if ($translatable)
			return _S($value);
		else
			return $value;
	}
}

?>
