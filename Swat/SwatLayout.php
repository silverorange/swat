<?
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
	 * @param string Filename of the layout XML file to load.
	 */
	function __construct($filename) {
		$xml = simplexml_load_file($filename);
			
		$this->widgets = array();
		$widget_tree =& $this->build($xml, $toplevel);
		//print_r(array_keys($this->widgets));
	}

	function &getWidget($name) {
		if (array_key_exists($name, $this->widgets))
			return $this->widgets[$name];
		else
			throw new SwatException(__CLASS__.": no widget named '$name'");
	}

	private function build($node, &$parent_widget) {
		foreach ($node->children() as $childname => $childnode) {

			$widget =& $this->buildWidget($childname, $childnode);

			$this->widgets[$widget->name] =& $widget;

			if ($parent_widget == null)
				$parent_widget =& $widget;
			else
				$parent_widget->add($widget);

			$this->build($childnode, $widget);
		}
	}

	private function buildWidget($name, $node) {

		$attrs = array();
		foreach ($node->attributes() as $attrname => $value) {
			$attrs[$attrname] = (string)$value;
		}

		switch ($name) {
			case 'SwatFrame':
				require_once('Swat/SwatFrame.php');
				$w = new SwatFrame();
				$this->getAttr($w, $attrs, 'title', 'string');
				break;
			case 'SwatForm':
				require_once('Swat/SwatForm.php');
				$w = new SwatForm();
				break;
			case 'SwatFormField':
				require_once('Swat/SwatFormField.php');
				$w = new SwatFormField();
				$this->getAttr($w, $attrs, 'title', 'string');
				break;
			case 'SwatDiv':
				require_once('Swat/SwatDiv.php');
				$w = new SwatDiv();
				$this->getAttr($w, $attrs, 'class', 'string');
				break;
			case 'SwatEntry':
				require_once('Swat/SwatEntry.php');
				$w = new SwatEntry();
				$this->getAttr($w, $attrs, 'required', 'bool');
				break;
			case 'SwatTextarea':
				require_once('Swat/SwatTextarea.php');
				$w = new SwatTextarea();
				$this->getAttr($w, $attrs, 'required', 'bool');
				break;
			case 'SwatCheckbox':
				require_once('Swat/SwatCheckbox.php');
				$w = new SwatCheckbox();
				$this->getAttr($w, $attrs, 'required', 'bool');
				break;
			case 'SwatFlydown':
				require_once('Swat/SwatFlydown.php');
				$w = new SwatFlydown();
				break;
			case 'SwatButton':
				require_once('Swat/SwatButton.php');
				$w = new SwatButton();
				$this->getAttr($w, $attrs, 'title', 'string');
				break;
			default:
				$w = null;
		}

		$this->getAttr($w, $attrs, 'name', 'string');

		return $w;
	}

	private function getAttr(&$w, &$attrs, $name, $type) {

		if (array_key_exists($name, $attrs)) {
			switch ($type) {
				case 'int':
					$w->$name = intval($attrs[$name]);
					break;
				case 'float':
					$w->$name = floatval($attrs[$name]);
					break;
				case 'bool':
					$w->$name = ($attrs[$name] == 'true') ? true : false;
					break;
				default:
					$w->$name = $attrs[$name];
					break;
			}
		}
	}
}
