<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container with a decorative frame and optional title
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFrame extends SwatContainer implements SwatTitleable
{
	/**
	 * A visible title for this frame, or null
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * An optional visible subtitle for this frame, or null
	 *
	 * @var string
	 */
	public $subtitle = null;

	/**
	 * Gets the title of this frame
	 *
	 * Implements the {SwatTitleable::getTitle()} interface.
	 *
	 * @return the title of this frame.
	 */
	public function getTitle()
	{
		if ($this->subtitle === null)
			return $this->title;

		if ($this->title === null)
			return $this->subtitle;

		return $this->title.': '.$this->subtitle;
	}

	/**
	 * Displays this frame
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		if ($this->id !== null)
			$outer_div->id = $this->id;

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

			$header_tag = new SwatHtmlTag('h'.$level);			
			$header_tag->class = 'swat-frame-title';
			$header_tag->setContent($this->title);

			if ($this->subtitle === null) {
				$header_tag->display();
			} else {
				$span_tag = new SwatHtmlTag('span');			
				$span_tag->class = 'swat-frame-subtitle';
				$span_tag->setContent($this->subtitle);

				$header_tag->open();
				$header_tag->displayContent();
				echo ' ';
				$span_tag->display();
				$header_tag->close();
			}
		}

		$inner_div->open();
		parent::display();
		$inner_div->close();
		$outer_div->close();
	}
}

?>
