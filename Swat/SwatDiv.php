<?
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container that surrounds it's contents in an HTML div tag.
 */
class SwatDiv extends SwatContainer {

	/**
	 * @var string Class to use on the HTML div tag.
	 */
	public $class = 'SwatDiv';

	public function display() {
		$divtag = new SwatHtmlTag('div');
		$divtag->class = $this->class;

		$divtag->open();

		foreach ($this->children as &$child)
			$child->display();

		$divtag->close();
	}
}

?>
