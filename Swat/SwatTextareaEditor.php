<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTextarea.php';
require_once 'Swat/SwatYUI.php';

/**
 * A what-you-see-is-what-you-get (WYSIWYG) XHTML textarea editor widget
 *
 * This textarea editor widget is powered by TinyMCE, which, like Swat is
 * licensed under the LGPL. See {@link http://tinymce.moxiecode.com/} for
 * details.
 *
 * @package   Swat
 * @copyright 2004-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTextareaEditor extends SwatTextarea
{
	// {{{ public properties

	/**
	 * Width
	 *
	 * Width of the editor. In percent, pixels, or ems.
	 *
	 * @var string
	 */
	public $width = '100%';

	/**
	 * Height
	 *
	 * Height of the editor. In percent, pixels, or ems.
	 *
	 * @var string
	 */
	public $height = '25em';

	/**
	 * Base-Href
	 *
	 * Optional base-href, used to reference images and other urls in the
	 * editor.
	 *
	 * @var string
	 */
	public $basehref = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new what-you-see-is-what-you-get XHTML textarea editor
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;
		$this->rows = 30;

		$this->addJavaScript(
			'packages/swat/javascript/tiny_mce/tiny_mce_src.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		// textarea tags cannot be self-closing when using HTML parser on XHTML
		$value = ($this->value === null) ? '' : $this->value;

		// escape value for display because we actually want to show entities
		// for editing
		$value = htmlspecialchars($value);

		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = 'swat-textarea-container yui-skin-sam';
		$div_tag->open();

		$textarea_tag = new SwatHtmlTag('textarea');
		$textarea_tag->name = $this->id;
		$textarea_tag->id = $this->id;
		$textarea_tag->class = $this->getCSSClassString();
		// NOTE: The attributes rows and cols are required in
		//       a textarea for XHTML strict.
		$textarea_tag->rows = $this->rows;
		$textarea_tag->cols = $this->cols;
		$textarea_tag->setContent($value);
		$textarea_tag->accesskey = $this->access_key;

		if (!$this->isSensitive())
			$textarea_tag->disabled = 'disabled';

		$textarea_tag->display();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

	/**
	 * Gets the id attribute of the XHTML element displayed by this widget
	 * that should receive focus
	 *
	 * @return string the id attribute of the XHTML element displayed by this
	 *                 widget that should receive focus or null if there is
	 *                 no such element.
	 *
	 * @see SwatWidget::getFocusableHtmlId()
	 */
	public function getFocusableHtmlId()
	{
		return null;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	protected function getInlineJavaScript()
	{
		$buttons = array(
			'bold',
			'italic',
			'|',
			'formatselect',
			'|',
			'removeformat',
			'|',
			'undo',
			'redo',
			'|',
			'outdent',
			'indent',
			'|',
			'bullist',
			'numlist',
			'|',
			'link',
			'image',
			'snippet',
		);

		$buttons = implode(',', $buttons);

		$formats = array(
			'p',
			'blockquote',
			'pre',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
		);

		$formats = implode(',', $formats);

		$config = array(
			'mode'                              => 'exact',
			'elements'                          => $this->id,
			'theme'                             => 'advanced',
			'theme_advanced_buttons1'           => $buttons,
			'theme_advanced_buttons2'           => '',
			'theme_advanced_buttons3'           => '',
			'theme_advanced_toolbar_location'   => 'top',
			'theme_advanced_toolbar_align'      => 'left',
			'theme_advanced_blockformats'       => $formats,
//			'theme_advanced_resizing'           => true,
//			'theme_advanced_resize_horizontal'  => false,
//			'theme_advanced_path'               => false,
//			'theme_advanced_statusbar_location' => 'bottom',
			'skin'                              => 'swat',
			'plugins'                           => 'swat,media',
		);

		ob_start();

		echo "tinyMCE.init({\n";

		$lines = array();
		foreach ($config as $name => $value) {
			if (is_string($value)) {
				$value = SwatString::quoteJavaScriptString($value);
			} elseif (is_bool($value)) {
				$value = ($value) ? 'true' : 'false';
			}
			$lines[] = "\t".$name.": ".$value;
		}

		echo implode(",\n", $lines);
		echo "\n});";

		return ob_get_clean();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this textarea
	 *
	 * @return array the array of CSS classes that are applied to this textarea.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-textarea-editor');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
