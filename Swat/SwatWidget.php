<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Base class for all widgets.
 */
abstract class SwatWidget extends SwatObject {

	/**
	 * @var SwatContainer The widget which contains this widget.
	 */
	public $parent = null;

	/**
	 * @var string A non-visible textual name for this widget, or null.
	 */
	public $name;

	/**
	 * @param string A non-visible textual name for this widget.
	 */
	function __construct($name = null) {
		$this->name = $name;
	}

	/**
	 * Display the widget.
	 *
	 * The widget displays itself as well as recursively displays any child widgets.
	 */
	abstract public function display();

	public function displayTest() {
		ob_start();
		$this->display();
		$buffer = ob_get_clean();
		$config = array('indent' => true,
		                'input-xml' => true,
		                'output-xml' => true,
		                'wrap' => 200);

		$tidy = tidy_parse_string($buffer, $config, 'UTF8');
		echo $tidy;
	}

	/**
	 * Process the widget.
	 *
	 * After a form submit, the widget processes itself as well as recursively
	 * processes any child widgets.
	 */
	public function process() {

	}

	/**
	 * Gather error messages.
	 *
	 * Gather all error messages from children of this widget and this widget itself.
	 *
	 * @return array Array of SwatErrorMessage objects.
	 */
	abstract function gatherErrorMessages();

}

?>
