<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container with a decorative frame and optional title
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFrame extends SwatContainer
{
	/**
	 * A visible title for this frame, or null
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Displays this frame
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title !== null) {
			/*
			 * Experimental: Header level is autocalculated based on the 
			 * level of the frame in the widget tree.  Top level frame
			 * is currently an <h2>.
			 */
			$level = 2;
			$ancestor = $this->parent;

			while ($ancestor !== null) {
				if ($ancestor instanceof SwatFrame)
					$level++;

				$ancestor = $ancestor->parent;
			}

			echo "<h{$level}>{$this->title}</h{$level}>";
		}

		$inner_div->open();
		parent::display();
		$inner_div->close();
		$outer_div->close();
	}
}

?>
