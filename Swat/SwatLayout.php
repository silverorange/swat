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
		$xml = simplexml_load_file($filename);

		$this->widgets = array();
		$widget_tree = $this->build($xml, $this->toplevel);
	}

	public function getWidget($name) {
		if (array_key_exists($name, $this->widgets))
			return $this->widgets[$name];
		else
			throw new SwatException(__CLASS__.": no widget named '$name'");
	}

	private function build($node, $parent_widget) {
		foreach ($node->children() as $childname => $childnode) {

			$widget = $this->buildWidget($childname, $childnode);

			$this->widgets[$widget->name] = $widget;

			if ($parent_widget == null)
				$parent_widget = $widget;
			else
				$parent_widget->add($widget);

			$this->build($childnode, $widget);
		}
	}

	private function buildWidget($name, $node) {

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

				else {
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
