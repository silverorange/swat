<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Generates a Swat widget tree from an XML layout file.
 */
class SwatLayout extends SwatObject {

	private $widgets;
	private $toplevel = null;
	private $classmap;

	/**
	 * @param string $filename Filename of the layout XML file to load.
	 */
	function __construct($filename, $classmap = null) {
		$this->classmap = $classmap;
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

		if ($xmlfile == null)
			throw new SwatException('SwatLayout: XML file not found: '.$filename);

		$xml = simplexml_load_file($xmlfile);

		$this->widgets = array();
		$widget_tree = $this->build($xml, $this->toplevel);
	}

	/**
	 * Retrieve a widget.
	 * Lookup a widget in the widget tree by name.
	 * @return SwatWidget A reference to the widget.
	 * @param string $name Name of the widget to retrieve.
	 */
	public function getWidget($name) {
		if (array_key_exists($name, $this->widgets))
			return $this->widgets[$name];
		else
			throw new SwatException(__CLASS__.": no widget named '$name'");
	}

	/**
	 * Retrieve the top widget.
	 * Lookup the widget at the root of the widget tree.
	 * @return SwatWidget A reference to the widget.
	 */
	public function getRoot() {
		return $this->toplevel;
	}

	private function build($node, $parent_widget) {
		foreach ($node->children() as $childname => $childnode) {

			$widget = $this->buildWidget($childname, $childnode, $parent_widget);

			if (class_exists('SwatWidget') && $widget instanceof SwatWidget)
				$this->widgets[$widget->name] = $widget;

			if ($parent_widget == null) {
				$this->toplevel = $widget;
				$parent_widget = $widget;

			} elseif (class_exists('SwatTableView') && 
				$parent_widget instanceof SwatTableView) {

				if ($widget instanceof SwatTableViewColumn)
					$parent_widget->appendColumn($widget);
				else
					throw new SwatException('SwatLayout: Only '.
						'SwatTableViewColumns can be nested within '.
						'SwatTableViews ('.$xmlfile.')');

			} elseif (class_exists('SwatTableViewColumn') &&
				$parent_widget instanceof SwatTableViewColumn) {

				if ($widget instanceof SwatCellRenderer)
					$parent_widget->addRenderer($widget);
				else
					throw new SwatException('SwatLayout: Only '.
						'SwatCellRenders can be nested within '.
						'SwatTableViewsColumns ('.$xmlfile.')');

			} else {
				$parent_widget->add($widget);
			}

			$this->build($childnode, $widget);
		}
	}

	private function buildWidget($name, $node, $parent_widget) {

		$classfile = "Swat/{$name}.php";

		if ($this->classmap != null) {
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
			$w->content = $this->parseAttribute('content', $content, $parent_widget);
		}

		foreach ($node->attributes() as $attrname => $attrvalue) {
			$attrname = (string)$attrname;
			$attrvalue = (string)$attrvalue;

			if (array_key_exists($attrname, $classvars))
				$w->$attrname = $this->parseAttribute($attrname, $attrvalue, $parent_widget);
			else
				throw new SwatException(__CLASS__.": no attribute named ".
					"'$attrname' in class $name");
		}
					
		return $w;
	}

	private function parseAttribute($name, $value, $parent_widget) {

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
			$parent_widget->linkField($field, $name);
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
