<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'jQuery/jQuery.php';

/**
 * An image cropping widget
 *
 * This widget uses JavaScript to present an adjustable boundry to define how
 * an image should be cropped.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageCropper extends SwatInputControl
{
	// {{{ public properties

	/**
	 * Image URL
	 *
	 * @var string
	 */
	public $image_url = null;

	/**
	 * Width of the image to display
	 *
	 * @var integer
	 */
	public $image_width = null;

	/**
	 * Height of the image to display
	 *
	 * @var integer
	 */
	public $image_height = null;

	/**
	 * Crop Ratio
	 *
	 * An optional width:height ratio to fix the crop-box to.
	 *
	 * @var float
	 */
	public $crop_box_ratio = null;

	// }}}
	// {{{ private properties

	/**
	 * Width of the crop bounding box
	 *
	 * @var integer
	 */
	private $crop_box_width = null;

	/**
	 * Height of the crop bounding box
	 *
	 * @var integer
	 */
	private $crop_box_height;

	/**
	 * Position of the left side of the crop bounding box
	 *
	 * @var integer
	 */
	private $crop_box_left;

	/**
	 * Position of the top side of the crop bounding box
	 *
	 * @var integer
	 */
	private $crop_box_top;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new change-order widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/jquery/javascript/jquery.js',
			jQuery::PACKAGE_ID);

		$this->addJavaScript('packages/jquery/javascript/iutil.js',
			jQuery::PACKAGE_ID);

		$this->addJavaScript('packages/jquery/javascript/iresizable.js',
			jQuery::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-image-cropper.css',
			Swat::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-image-cropper.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		parent::process();

		$data = $this->getForm()->getFormData();

		$this->crop_width = $data[$this->id.'_width'];
		$this->crop_height = $data[$this->id.'_height'];
		$this->crop_x = $data[$this->id.'_x'];
		$this->crop_y = $data[$this->id.'_y'];

		echo $this->crop_x.' '.$this->crop_y.' '.$this->crop_width.' '.$this->crop_height;

		exit();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this change-order control
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		?>
		<div class="swat-image-cropper" id="<?=$this->id?>">
		<div class="swat-image-cropper-background"></div>
		<div class="swat-image-cropper-box">
			<div class="swat-image-cropper-se" id="<?=$this->id?>se"></div>
			<div class="swat-image-cropper-e" id="<?=$this->id?>e"></div>
			<div class="swat-image-cropper-ne" id="<?=$this->id?>ne"></div>
			<div class="swat-image-cropper-n" id="<?=$this->id?>n"></div>
			<div class="swat-image-cropper-nw" id="<?=$this->id?>nw"></div>
			<div class="swat-image-cropper-w" id="<?=$this->id?>w"></div>
			<div class="swat-image-cropper-sw" id="<?=$this->id?>sw"></div>
			<div class="swat-image-cropper-s" id="<?=$this->id?>s"></div>
		</div>
		</div>
		<?

		$this->setCropBoxDimensions();

		$this->getForm()->addHiddenField($this->id.'_width', $this->crop_box_width);
		$this->getForm()->addHiddenField($this->id.'_height', $this->crop_box_height);
		$this->getForm()->addHiddenField($this->id.'_x', $this->crop_box_left);
		$this->getForm()->addHiddenField($this->id.'_y', $this->crop_box_top);

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required by this change-order control
	 *
	 * @return string the inline JavaScript required by this change-order
	 *                 control.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf('%1$s_obj = new SwatImageCropper("%1$s");
			%1$s_obj.image_url = "%2$s";
			%1$s_obj.image_width = %3$d;
			%1$s_obj.image_height = %4$d;
			%1$s_obj.crop_box_width = %5$d;
			%1$s_obj.crop_box_height = %6$d;
			%1$s_obj.crop_box_left = %7$d;
			%1$s_obj.crop_box_top = %8$d;
			%1$s_obj.crop_box_ratio = %9$s;',
			$this->id,
			$this->image_url,
			$this->image_width,
			$this->image_height,
			$this->crop_box_width,
			$this->crop_box_height,
			$this->crop_box_left,
			$this->crop_box_top,
			($this->crop_box_ratio === null) ? 'null' : $this->crop_box_ratio);
	}

	// }}}
	// {{{ private function setCropBoxDimensions()

	private function setCropBoxDimensions()
	{
		if ($this->crop_box_ratio === null) {
			$this->crop_box_width = $this->image_width;
			$this->crop_box_height = $this->image_height;
		} elseif ($this->crop_box_ratio > 1) {
			$this->crop_box_width = $this->image_height / $this->crop_box_ratio;
			$this->crop_box_height = $this->image_height;
		} else {
			$this->crop_box_width = $this->image_width;
			$this->crop_box_height = $this->image_width * $this->crop_box_ratio;
		}

		$this->crop_box_left = ($this->crop_box_width != $this->image_width) ?
			(($this->image_width - $this->crop_box_width) / 2) : 0;

		$this->crop_box_top = ($this->crop_box_height != $this->image_height) ?
			(($this->image_height - $this->crop_box_height) / 2) : 0;
	}

	// }}}
}

?>
