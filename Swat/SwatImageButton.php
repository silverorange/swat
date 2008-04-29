<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatButton.php';

/**
 * An image button widget
 *
 * This widget displays as an XHTML form image button, so it must be used
 * within {@link SwatForm}.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageButton extends SwatButton
{
	// {{{ public properties

	/**
	 * Image
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $image;

	/**
	 * Optional array of values to substitute into the image property
	 *
	 * Uses vsprintf() syntax, for example:
	 *
	 * <code>
	 * $my_image->image = 'mydir/%s.%s';
	 * $my_image->values = array('myfilename', 'ext');
	 * </code>
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $height = null;

	/**
	 * Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = null;

	// }}}
	// {{{ public function process()

	/**
	 * Does button processing
	 *
	 * Sets whether this button has been clicked and also updates the form
	 * this button belongs to with a reference to this button if this button
	 * submitted the form.
	 */
	public function process()
	{
		SwatWidget::process();

		$data = &$this->getForm()->getFormData();

		// images submit id_x, and id_y post vars
		if (isset($data[$this->id.'_x'])) {
			$this->clicked = true;
			$this->getForm()->button = $this;
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this image button
	 *
	 * Outputs an XHTML input tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'image';
		$input_tag->id = $this->id;
		$input_tag->name = $this->id;
		$input_tag->value = $this->title;
		$input_tag->class = $this->getCSSClassString();

		if (count($this->values))
			$input_tag->src = vsprintf($this->image, $this->values);
		else
			$input_tag->src = $this->image;

		if ($this->height !== null)
			$input_tag->height = $this->height;

		if ($this->width !== null)
			$input_tag->width = $this->width;

		if ($this->title !== null)
			$input_tag->title = $this->title;

		$input_tag->tabindex = $this->tab_index;
		$input_tag->accesskey = $this->access_key;

		$input_tag->display();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this button
	 *
	 * @return array the array of CSS classes that are applied to this button.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-image-button');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
