<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A a tool link in the widget tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolLink extends SwatControl
{
	/**
	 * Link href
	 *
	 * The link may include a sprintf substitution tag. For example:
	 * <code>
	 * $my_link->link = 'MySection/MyPage?id=%s';
	 * </code>
	 *
	 * @var string
	 */
	public $link = '';

	/**
	 * The title of this link
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * An optional image for this tool link
	 *
	 * @var string
	 */
	public $image = null;

	/**
	 * The width of the optional image
	 *
	 * @var integer
	 */
	public $image_width = null;

	/**
	 * The height of the optional image
	 *
	 * @var integer
	 */
	public $image_height = null;

	/**
	 * A value to substitute into the link
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Displays this tool link
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$anchor_tag = new SwatHtmlTag('a');

		if ($this->value === null)
			$anchor_tag->href = $this->link;
		else
			$anchor_tag->href = sprintf($this->link, $this->value);

		$anchor_tag->class = 'swat-tool-link';

		if ($this->image === null) {
			$anchor_tag->content = $this->title;
			$anchor_tag->display();
		} else {
			$image_tag = new SwatHtmlTag('img');
			$image_tag->src = $this->image;
			if ($this->image_width !== null)
				$image_tag->width = $this->image_width;

			if ($this->image_height !== null)
				$image_tag->height = $this->image_height;

			$image_tag->alt = $this->title;

			$anchor_tag->open();
			echo $this->title;
			$image_tag->display();
			$anchor_tag->close();
		}
	}

	/**
	 * Sets the image of this tool link to a stock image
	 *
	 * Valid stock image ids are:
	 *
	 * - edit
	 *
	 * @param string $stock_id the identifier of the stock image to use.
	 *
	 * @throws SwatException
	 */
	public function setImageFromStock($stock_id)
	{
		switch ($stock_id) {
		case 'edit':
			$this->image = 'edit.png';
			$this->image_width = '16';
			$this->image_height = '16';
			break;
		default:
			throw new SwatException(sprintf("%s: no stock image with the id ".
				"of '%s' exists.",
				__CLASS__,
				$stock_id));
		}
	}
}

?>
