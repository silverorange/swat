<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatYUI.php';

/**
 * An image cropping widget
 *
 * This widget uses JavaScript to present an adjustable boundry to define how
 * an image should be cropped.
 *
 * This uses a YUI-based image cropping widget licensed under a BSD license
 * written by Julien Lecomte. Since this widget is not distributed with YUI,
 * the code is included in Swat. There are small local modifications present in
 * Swat to fix some CSS issues in IE6. See
 * {@link http://www.julienlecomte.net/blog/2007/07/24/yui-based-image-cropper-widget/}
 * for details about the YUI-based widget.
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageCropper extends SwatInputControl
{
	// {{{ public properties

	/**
	 * Image URI
	 *
	 * @var string
	 */
	public $image_uri;

	/**
	 * Width of the image to display
	 *
	 * @var integer
	 */
	public $image_width;

	/**
	 * Height of the image to display
	 *
	 * @var integer
	 */
	public $image_height;

	/**
	 * Optional width:height ratio to enforce for the cropped area
	 *
	 * @var float
	 */
	public $crop_box_ratio;

	/**
	 * Width of the crop bounding box
	 *
	 * @var integer
	 */
	public $crop_box_width;

	/**
	 * Height of the crop bounding box
	 *
	 * @var integer
	 */
	public $crop_box_height;

	/**
	 * Position of the left side of the crop bounding box
	 *
	 * @var integer
	 */
	public $crop_box_left;

	/**
	 * Position of the top side of the crop bounding box
	 *
	 * @var integer
	 */
	public $crop_box_top;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new image cropper
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'event', 'dragdrop'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript(
			'packages/swat/javascript/swat-yui-image-cropper.js',
			SwatYUI::PACKAGE_ID);

		$this->addStyleSheet(
			'packages/swat/styles/swat-yui-image-cropper.css',
			SwatYUI::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-image-cropper.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		parent::process();

		$data = $this->getForm()->getFormData();

		$this->crop_box_width  = $data[$this->id.'_width'];
		$this->crop_box_height = $data[$this->id.'_height'];
		$this->crop_box_left   = $data[$this->id.'_x'];
		$this->crop_box_top    = $data[$this->id.'_y'];
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this image cropper
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$this->autoCropBoxDimensions();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'swat-image-cropper';
		$div_tag->open();

		$image_tag = new SwatHtmlTag('img');
		$image_tag->id = $this->id.'_image';
		$image_tag->src = $this->image_uri;
		$image_tag->width = $this->image_width;
		$image_tag->height = $this->image_height;
		$image_tag->alt = Swat::_('Crop Image');
		$image_tag->display();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';

		$input_tag->id = $this->id.'_width';
		$input_tag->name = $this->id.'_width';
		$input_tag->value = $this->crop_box_width;
		$input_tag->display();

		$input_tag->id = $this->id.'_height';
		$input_tag->name = $this->id.'_height';
		$input_tag->value = $this->crop_box_height;
		$input_tag->display();

		$input_tag->id = $this->id.'_x';
		$input_tag->name = $this->id.'_x';
		$input_tag->value = $this->crop_box_left;
		$input_tag->display();

		$input_tag->id = $this->id.'_y';
		$input_tag->name = $this->id.'_y';
		$input_tag->value = $this->crop_box_top;
		$input_tag->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this image cropper
	 *
	 * @return string the inline JavaScript required by this image cropper.
	 */
	protected function getInlineJavaScript()
	{
		$options = array();

		if ($this->crop_box_width !== null)
			$options['w'] = $this->crop_box_width;

		if ($this->crop_box_height !== null)
			$options['h'] = $this->crop_box_height;

		if ($this->crop_box_left !== null)
			$options['x'] = $this->crop_box_left;

		if ($this->crop_box_top !== null)
			$options['y'] = $this->crop_box_top;

		if ($this->crop_box_ratio !== null)
			$options['xyratio'] = $this->crop_box_ratio;

		$options_string = '';
		$first = true;
		foreach ($options as $key => $value) {
			if ($first)
				$first = false;
			else
				$options_string.= ', ';

			$options_string.= sprintf("%s: %F", $key, $value);
		}

		return sprintf("%1\$s_obj = new SwatImageCropper(".
			"'%1\$s', {%2\$s});", $this->id, $options_string);
	}

	// }}}
	// {{{ protected function autoCropBoxDimensions()

	/**
	 * Automatically sets crop box dimensions if they are not specified and
	 * constrains crop box dimensions to image size
	 *
	 * Crop dimensions are automatically set as large as possible and centred
	 * on the image if they are not specified. If the specified crop dimensions
	 * are outside the image dimensions, the x and y coordinates are first
	 * placed inside the image and then the width and height are adjusted to
	 * make the crop box fit inside the image dimensions.
	 */
	protected function autoCropBoxDimensions()
	{
		// fix bad ratio
		if ($this->crop_box_ratio == 0)
			$this->crop_box_ratio = null;

		// autoset width
		if ($this->crop_box_width === null) {
			if ($this->crop_box_ratio === null || $this->crop_box_ratio <= 1)
				$this->crop_box_width = $this->image_width;
			else
				$this->crop_box_width =
					round($this->image_height / $this->crop_box_ratio);
		}

		// autoset height
		if ($this->crop_box_height === null) {
			if ($this->crop_box_ratio === null || $this->crop_box_ratio > 1)
				$this->crop_box_height = $this->image_height;
			else
				$this->crop_box_height =
					round($this->image_width * $this->crop_box_ratio);
		}

		// autoset left
		if ($this->crop_box_left === null) {
			if ($this->crop_box_width < $this->image_width)
				$this->crop_box_left =
					round(($this->image_width - $this->crop_box_width) / 2);
			else
				$this->crop_box_left = 0;
		}

		// autoset top
		if ($this->crop_box_top === null) {
			if ($this->crop_box_height < $this->image_height)
				$this->crop_box_top =
					round(($this->image_height - $this->crop_box_height) / 2);
			else
				$this->crop_box_top = 0;
		}

		// constrain dimensions to image size
		$this->crop_box_left = max($this->crop_box_left, 0);
		$this->crop_box_left =
			min($this->crop_box_left, $this->image_width - 2);

		$this->crop_box_top = max($this->crop_box_top, 0);
		$this->crop_box_top =
			min($this->crop_box_top, $this->image_height - 2);

		if ($this->crop_box_left + $this->crop_box_width > $this->image_width)
			$this->crop_box_width = $this->image_width - $this->crop_box_left;

		if ($this->crop_box_top + $this->crop_box_height > $this->image_height)
			$this->crop_box_height = $this->image_height - $this->crop_box_top;
	}

	// }}}
}

?>
