<?
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A flydown (aka combo-box) selection widget.
 */
class SwatFlydown extends SwatControl {
	
	/**
	 * @var array An array of options for the flydown in the form value => title.
	 */
	public $options = null;

	/**
	 * @var string The value of the selected option, or null.
	 */
	public $value = null;

	function display() {
		$selecttag = new SwatHtmlTag('select');
		$selecttag->name = $this->name;
		$selecttag->id = $this->name;

		$optiontag = new SwatHtmlTag('option');

		$selecttag->open();

		if ($this->options != null) {
			foreach ($this->options as $value => $title) {
				$optiontag->value = $value;
				$optiontag->removeAttr('selected');

				if ($this->value == $value)
					$optiontag->selected = null;

				$optiontag->open();
				echo $title;
				$optiontag->close();
			}
		}

		$selecttag->close();
	}	

	function process() {
		$this->value = $_POST[$this->name];
	}
}

?>
