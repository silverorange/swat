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

	/**
	 * @param string $filename Filename of the layout XML file to load.
	 */
	function __construct($filename) {
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

			if ($widget instanceof SwatWidget)
				$this->widgets[$widget->name] = $widget;

			if ($parent_widget == null) {
				$this->toplevel = $widget;
				$parent_widget = $widget;

			} elseif ($parent_widget instanceof SwatTableView) {
				if ($widget instanceof SwatTableViewColumn)
					$parent_widget->appendColumn($widget);
				else
					throw new SwatException('SwatLayout: Only '.
						'SwatTableViewColumns can be nested within '.
						'SwatTableViews ('.$xmlfile.')');

			} elseif ($parent_widget instanceof SwatTableViewColumn) {
				if ($widget instanceof SwatCellRenderer)
					$parent_widget->renderer = $widget;
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

		require_once("Swat/$name.php");
		$w = eval(sprintf("return new %s();", $name));
		$classvars = get_class_vars($name);
		//print_r($classvars);

		if (array_key_exists('text', $classvars))
			$w->text = (string)$node; // stuff between opening and closing tags

		foreach ($node->attributes() as $attrname => $attrvalue) {
			$attrname = (string)$attrname;
			$attrvalue = (string)$attrvalue;

			if (array_key_exists($attrname, $classvars)) {

				if (strncmp($attrvalue, 'bool:', 5) == 0)
					$w->$attrname = (substr($attrvalue, 5) == 'true') ? true : false;
				elseif (strncmp($attrvalue, 'int:', 4) == 0)
					$w->$attrname = intval(substr($attrvalue, 4));

				elseif (strncmp($attrvalue, 'float:', 6) == 0)
					$w->$attrname = floatval(substr($attrvalue, 6));

				elseif (strncmp($attrvalue, 'string:', 7) == 0)
					$w->$attrname = substr($attrvalue, 7);

				elseif (strncmp($attrvalue, 'data:', 5) == 0) {
					$field = substr($attrvalue, 5);
					$parent_widget->linkField($field, $attrname);

				} else {
					if ($attrvalue == 'false' || $attrvalue == 'true' )
						trigger_error(__CLASS__.": Possible missing 'bool:' ".
							"on attribute $attrname", E_USER_NOTICE);

					if (is_numeric($attrvalue))
						trigger_error(__CLASS__.": Possible missing 'int:' or ".
							"'float:' on attribute $attrname", E_USER_NOTICE);

					$w->$attrname = $attrvalue;
				}

			} else {
				throw new SwatException(__CLASS__.": no attribute named ".
					"'$attrname' in class $name");
			}
		}
					
		return $w;
	}
}
