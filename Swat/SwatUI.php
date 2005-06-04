<?php

require_once('Swat/SwatObject.php');
require_once('Swat/SwatContainer.php');

/**
 * Generates a Swat widget tree from an XML UI file
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatUI extends SwatObject {

	protected $classmap = null;

	private $widgets;
	private $root;

	function __construct() {
		$this->widgets = array();
		$this->root = new SwatContainer();
	}

	/**
	 * Load a UI from and XML file
	 *
	 * @param string $filename Filename of the XML UI file to load.
	 */
	public function loadFromXML($filename) {
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
			throw new SwatException('SwatUI: XML file not found: '.$filename);

		$xml = simplexml_load_file($xmlfile);

		$this->parseUI($xml, $this->root);
	}

	/**
	 * Retrieve a widget
	 *
	 * Lookup a widget in the widget tree by id.
	 *
	 * @param string $id Id of the widget to retrieve.
	 * @param boolean $silent If true, return null instead of throwing an 
	 *        exception if the widget is not found.
	 *
	 * @return SwatWidget A reference to the widget.
	 */
	public function getWidget($id, $silent = false) {
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
	 * Retrieve the top widget
	 *
	 * Lookup the widget at the root of the widget tree.
	 *
	 * @return SwatWidget A reference to the widget.
	 */
	public function getRoot() {
		return $this->root;
	}

	private function parseUI($node, $parent_widget) {
		$widgets = array();
	
		foreach ($node->children() as $childname => $childnode) {
			if ($childname == 'property')
				$this->parseProperty($parent_widget, $childnode);
			else {
				$parsed_node = $this->parseNode($childnode);

				if ($childname == 'widget') {
					if (class_exists('SwatWidget') && $parsed_node instanceof SwatWidget
						 && $parsed_node->id != null) {

						if (isset($this->widgets[$parsed_node->id]))
							throw new SwatException(__CLASS__.
								": widget with an id of '{$parsed_node->id}' ".
								"already exists.");

						$this->widgets[$parsed_node->id] = $parsed_node;

					} elseif (!(class_exists('SwatWidget') && $parsed_node instanceof SwatWidget)) {
						throw new SwatException(__CLASS__.
							': '.get_class($parsed_node).' is declared as a widget '.
							'but is not an instance of SwatWidget.');
					}
				} elseif ($childname == 'object') {
					if (class_exists('SwatWidget') && $parsed_node instanceof SwatWidget) {
						throw new SwatException(__CLASS__.
							': '.get_class($parsed_node).' is declared as an object '.
							'but is an instance of SwatWidget and should be declared as a widget.');
					}
				}
	
				$this->attachToParent($parsed_node, $parent_widget);
				$this->parseUI($childnode, $parsed_node);
			}
		}
	}

	private function attachToParent($widget, $parent) {
		if ($parent instanceof SwatUIParent)
			$parent->addChild($widget);
		else
			throw new SwatException(__CLASS__.
				': '.get_class($parent).' does not implement SwatUIParent.');

	}

	private function parseNode($node) {
		if (isset($node['class']))
			$class = (string)$node['class'];
		else
			throw new SwatException("Widget/Object is missing 'class' property.");
	
		$classfile = "Swat/{$class}.php";

		if ($this->classmap !== null) {
			foreach ($this->classmap as $prefix => $path) {
				if (strncmp($class, $prefix, strlen($prefix)) == 0)
					$classfile = "{$path}/{$class}.php";
			}
		}

		require_once($classfile);
		$node_class = new $class();
	
		if (isset($node['id']))
			$node_class->id = (string)$node['id'];

		return $node_class;
	}

	private function parseProperty($widget, $property) {
		$classvars = get_class_vars(get_class($widget));
		
		if (!isset($property['name'])) {
			throw new SwatException(sprintf(__CLASS__.
				": property missing 'name' ".
				"'%s' for class %s"), $attrvalue, get_class($widget));
		
		} elseif (!isset($property['value'])) {
			throw new SwatException(sprintf(__CLASS__.
				": property missing 'value' ".
				"'%s' for class %s"), $attrvalue, get_class($widget));
		
		} elseif (!array_key_exists((string)$property['name'], $classvars)) {
			throw new SwatException(sprintf(__CLASS__.
				": no attribute named '%s' in class %s",
				(string)$property['name'], get_class($widget)));
				
		} else {
			$name = (string)$property['name'];
			$value = (string)$property['value'];
			$translatable = (isset($property['translatable'])
				&& strtolower((string)$property['translatable']) == 'yes');

			$type = (isset($property['type'])) ?
				(string)$property['type'] : null;
			
			$widget->$name = $this->parseValue($name, $value, $type,
				$translatable, $widget);
		}
	}

	private function parseValue($name, $value, $type, $translatable, $widget) {
		switch ($type) {
			case 'boolean':
				return ($value == 'true')  ? true : false;
			case 'integer':
				return intval($value);
			case 'float':
				return floatval($value);
			case 'string':
				return $this->translateValue($value, $translatable);
			case 'data':
				$widget->parent->linkField($widget, $value, $name);
				return null;
			default:
				if ($value == 'false' || $value == 'true' )
					trigger_error(__CLASS__.": Possible missing 'boolean:' ".
						"on attribute {$name}", E_USER_NOTICE);

				if (is_numeric($value))
					trigger_error(__CLASS__.": Possible missing 'integer:' or ".
						"'float:' on attribute {$name}", E_USER_NOTICE);
				
				return $this->translateValue($value, $translatable);
		}

	}

	private function translateValue($value, $translatable) {
		if ($translatable)
			return _S($value);
		else
			return $value;
	}
}

?>
