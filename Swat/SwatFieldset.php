<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * An HTML fieldset tag with an optional HTML legend title.
 */
class SwatFieldset extends SwatContainer {

	/**
	 * @var string A visible name for this fieldset, or null.
	 */
	public $title = null;

	public function display() {
		$fieldsettag = new SwatHtmlTag('fieldset');
		$fieldsettag->class = 'swat-fieldset';

		$fieldsettag->open();

		if ($this->title != null) {
			$legendtag = new SwatHtmlTag('legend');
			$legendtag->open();
			echo $this->title;
			$legendtag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		$fieldsettag->close();
	}
}

?>
