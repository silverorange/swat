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
	 * Lookup a widget in the widget tree by name.
	 *
	 * @param string $name Name of the widget to retrieve.
	 * @param boolean $silent If true, return null instead of throwing an 
	 *        exception if the widget is not found.
	 * @return SwatWidget A reference to the widget.
	 */
	public function getWidget($name, $silent = false) {
		if (array_key_exists($name, $this->widgets))
			return $this->widgets[$name];
		else
			if ($silent)
				return null;
			else
				throw new SwatException(__CLASS__.": no widget named '$name'");
	}

	/**
	 * Retrieve the top widget
	 *
	 * Lookup the widget at the root of the widget tree.
	 * @return SwatWidget A reference to the widget.
	 */
	public function getRoot() {
		return $this->root;
	}

	private function parseUI($node, $parent_widget) {
		foreach ($node->children() as $childname => $childnode) {

			$widget = $this->parseWidget($childname, $childnode, $parent_widget);

			if (class_exists('SwatWidget') && $widget instanceof SwatWidget
				 && $widget->name != null) {

				if (isset($this->widgets[$widget->name]))
					throw new SwatException(__CLASS__.
						": widget named '{$widget->name}' already exists.");

				$this->widgets[$widget->name] = $widget;
			}

			$this->attachToParent($widget, $parent_widget);
			$this->parseUI($childnode, $widget);
		}
	}

	private function attachToParent($widget, $parent) {

		if ($parent instanceof SwatParent)
			$parent->addChild($widget);
		else
			throw new SwatException(__CLASS__.
				': '.get_class($parent).' does not implement SwatParent.');

	}

	private function parseWidget($name, $node, $parent_widget) {

		$classfile = "Swat/{$name}.php";

		if ($this->classmap !== null) {
			foreach ($this->classmap as $prefix => $path) {
				if (strncmp($name, $prefix, strlen($prefix)) == 0)
					$classfile = "{$path}/{$name}.php";
			}
		}

		require_once($classfile);
		$w = eval(sprintf("return new %s();", $name));
		$classvars = get_class_vars($name);

		if (array_key_exists('content', $classvars)) {
			$content = trim((string)$node); // stuff between opening and closing tags
			$w->content = $this->parseAttribute('content', $content, $w, $parent_widget);
		}

		foreach ($node->attributes() as $attrname => $attrvalue) {
			$attrname = (string)$attrname;
			$attrvalue = (string)$attrvalue;

			if (array_key_exists($attrname, $classvars))
				$w->$attrname = $this->parseAttribute($attrname, $attrvalue, $w, $parent_widget);
			else
				throw new SwatException(__CLASS__.": no attribute named ".
					"'$attrname' in class $name");
		}
					
		return $w;
	}

	private function parseAttribute($name, $value, $widget, $parent_widget) {

		if (strncmp($value, 'bool:', 5) == 0)
			$ret = (substr($value, 5) == 'true') ? true : false;

		elseif (strncmp($value, 'int:', 4) == 0)
			$ret = intval(substr($value, 4));

		elseif (strncmp($value, 'float:', 6) == 0)
			$ret = floatval(substr($value, 6));

		elseif (strncmp($value, 'string:', 7) == 0)
			$ret = substr($value, 7);

		elseif (strncmp($value, 'data:', 5) == 0) {
			$field = substr($value, 5);
			$parent_widget->linkField($widget, $field, $name);
			$ret = null;

		} else {
			if ($value == 'false' || $value == 'true' )
				trigger_error(__CLASS__.": Possible missing 'bool:' ".
					"on attribute $name", E_USER_NOTICE);

			if (is_numeric($value))
				trigger_error(__CLASS__.": Possible missing 'int:' or ".
					"'float:' on attribute $name", E_USER_NOTICE);

			$ret = $value;
		}

		return $ret;
	}
}
