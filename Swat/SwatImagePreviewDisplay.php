<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatImageDisplay.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Image preview display control
 *
 * This control displays an image and uses a lightbox-like effect to display
 * another image when the first image is clicked.
 *
 * @package   Swat
 * @copyright 2005-2008 silverorange
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

	/**
	 * Whether or not to show a resize icon next to the image
	 *
	 * @var boolean
	 */
	public $show_icon = true;

	/**
	 * The href attribute in the XHTML anchor tag
	 *
	 * If JavaScript is not enabled, the image preview display will link to
	 * this location
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $renderer->link = 'MySection/MyPage/%s?id=%s';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatImagePreviewDisplay::$link_value
	 */
	public $link;

	/**
	 * A value or array of values to substitute into the link
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatImageLinkCellRenderer::$link} property. If the value
	 * is a string a single sprintf() call is made.
	 *
	 * @var mixed
	 *
	 * @see SwatImagePreviewDisplay::$link
	 */
	public $link_value = null;

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

		$this->title = Swat::_('View Larger Image');
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

		if ($this->preview_image === null) {
			parent::display();
		} else {
			if ($this->link !== null) {
				$tag = new SwatHtmlTag('a');
				if ($this->link_value === null) {
					$tag->href = $this->link;
				} elseif (is_array($this->link_value)) {
					$tag->href = vsprintf($this->link, $this->link_value);
				} else {
					$tag->href = sprintf($this->link, $this->link_value);
				}
			} else {
				$tag = new SwatHtmlTag('span');
			}

			$tag->id = $this->id.'_wrapper';
			$tag->title = $this->title;

			if ($this->show_icon) {
				$tag->class = 'swat-image-preview-display-link';
			} else {
				$tag->class = 'swat-image-preview-display-link-plain';
			}

			$tag->open();
			parent::display();
			$tag->close();

			Swat::displayInlineJavaScript($this->getInlineJavaScript());
		}
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
		static $shown = false;

		if (!$shown) {
			$javascript = $this->getInlineJavaScriptTranslations();
			$shown = true;
		} else {
			$javascript = '';
		}

		$javascript.= sprintf(
			"var %s = new SwatImagePreviewDisplay('%s', '%s', %d, %d);",
			$this->id,
			$this->id,
			$this->preview_image,
			$this->preview_width,
			$this->preview_height);

		return $javascript;
	}

	// }}}
	// {{{ protected function getInlineJavaScriptTranslations()

	/**
	 * Gets translatable string resources for the JavaScript object for
	 * this widget
	 *
	 * @return string translatable JavaScript string resources for this widget.
	 */
	protected function getInlineJavaScriptTranslations()
	{
		$close_text  = Swat::_('Close');

		return sprintf(
			"SwatImagePreviewDisplay.close_text = '%s';\n",
			$close_text);
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
