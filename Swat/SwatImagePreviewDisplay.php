<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatImageDisplay.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Image display control
 *
 * This control simply displays a static image.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImagePreviewDisplay extends SwatImageDisplay
{
	// {{{ public properties

	/**
	 * Preview Image
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $preview_image;

	/**
	 * Optional array of values to substitute into the preview image property
	 *
	 * Uses vsprintf() syntax, for example:
	 *
	 * <code>
	 * $my_image->preview_image = 'mydir/%s.%s';
	 * $my_image->preview_image_values = array('myfilename', 'ext');
	 * </code>
	 *
	 * @var array
	 */
	public $preview_image_values = array();

	/**
	 * Preview Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $preview_height = null;

	/**
	 * Preview Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $preview_width = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new image preview display
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

		$this->addJavaScript(
			'packages/swat/javascript/swat-image-preview-display.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet(
			'packages/swat/styles/swat-image-preview-display.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this image
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->id = $this->id.'_link';
		$anchor_tag->title = Swat::_('View Larger Image');
		$anchor_tag->class = 'swat-image-preview-display-link';
		$anchor_tag->href = $this->preview_image;
		$anchor_tag->open();

		parent::display();

		$anchor_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets inline JavaScript required by this image preview.
	 *
	 * @return string inline JavaScript needed by this widget.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf(
			"var %s = new SwatImagePreviewDisplay('%s', '%s', %d, %d);",
			$this->id,
			$this->id,
			$this->preview_image,
			$this->preview_width,
			$this->preview_height);

		return $javascript;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this image display
	 *
	 * @return array the array of CSS classes that are applied to this image
	 *                display.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-image-preview-display');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
