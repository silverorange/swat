<?
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatException.php');

/**
 * A checkbox entry widget.
 */
class SwatCheckbox extends SwatControl {

	public $value = false;
	
	function display() {
		$inputtag = new SwatHtmlTag('input');
		$inputtag->type = 'checkbox';
		$inputtag->name = $this->name;
		$inputtag->id = $this->name;
		$inputtag->value = '1';

		if ($this->value)
			$inputtag->checked = null;

		$inputtag->display();
	}	

	function process() {
		$this->value = array_key_exists($this->name, $_POST);
	}
}

