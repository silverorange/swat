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
	 * A visible name for this fieldset, or null.
	 * @var string
	 */
	public $title = null;

	public function display() {
		$fieldset_tag = new SwatHtmlTag('fieldset');
		$fieldset_tag->class = 'swat-fieldset';

		$fieldset_tag->open();

		if ($this->title != null) {
			$legend_tag = new SwatHtmlTag('legend');
			$legend_tag->open();
			echo $this->title;
			$legend_tag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		$fieldset_tag->close();
	}
}

?>
