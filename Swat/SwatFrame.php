<?
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container with a decorative frame and optional title.
 */
class SwatFrame extends SwatContainer {

	/**
	 * @var string A visible name for this frame, or null.
	 */
	public $title = null;

	public function display() {
		$outer_divtag = new SwatHtmlTag('div');
		$outer_divtag->class = 'SwatFrame';

		$inner_divtag = new SwatHtmlTag('div');
		$inner_divtag->class = 'SwatFrameContents';

		$outer_divtag->open();

		if ($this->title != null) {
			echo '<h2>', $this->title, '</h2>';
		}

		$inner_divtag->open();

		foreach ($this->children as &$child)
			$child->display();

		$inner_divtag->close();
		$outer_divtag->close();
	}
}

?>
