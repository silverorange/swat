<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTextarea.php';
require_once 'Swat/SwatYUI.php';

/**
 * A wysiwyg text entry widget
 *
 * @package   Swat
 * @copyright 2004-2008 silverorange
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
	 * Creates a new wysiwyg textarea editor
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$yui = new SwatYUI(array('editor'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript(
			'packages/swat/javascript/swat-textarea-editor.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-textarea-editor.css',
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
		static $shown = false;

		if (!$shown) {
			$javascript = $this->getInlineJavaScriptTranslations();
			$shown = true;
		} else {
			$javascript = '';
		}

		$javascript.= sprintf(
			"var %s_obj = new SwatTextareaEditor('%s', '%s', '%s');",
			$this->id,
			$this->id,
			$this->width,
			$this->height);

		return $javascript;
	}

	// }}}
	// {{{ protected function getInlineJavaScriptTranslations()

	protected function getInlineJavaScriptTranslations()
	{
		// TODO
		$javascript = '';
		return $javascript;
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
	// {{{ private function getTranslations()

	private function getTranslations()
	{
		return array(
			'bold'             => Swat::_('Bold'),
			'italic'           => Swat::_('Italic'),
			'underline'        => Swat::_('Underline'),
			'align_left'       => Swat::_('Align Left'),
			'align_right'      => Swat::_('Align Right'),
			'align_center'     => Swat::_('Align Center'),
			'ordered_list'     => Swat::_('Ordered List'),
			'unordered_list'   => Swat::_('Unordered List'),
			'indent'           => Swat::_('Indent'),
			'outdent'          => Swat::_('Outdent'),
			'insert_link'      => Swat::_('Insert Link'),
			'horizontal_rule'  => Swat::_('Horizontal Rule'),
			'highlight'        => Swat::_('Highlight'),
			'quote'            => Swat::_('Quote'),
			'style'            => Swat::_('Style'),
			'clear_formatting' => Swat::_('Clear Formatting'),
			'paragraph'        => Swat::_('Paragraph'),
			'heading'          => Swat::_('Heading'),
			'address'          => Swat::_('Address'),
			'formatted'        => Swat::_('Formatted'),

			//pop-up link
			'enter_url'        => Swat::_('A URL is required'),
			'url'              => Swat::_('URL'),
			'link_text'        => Swat::_('Link Text'),
			'target'           => Swat::_('Target'),
			'insert_link'      => Swat::_('Insert Link'),
			'cancel'           => Swat::_('Cancel'),
		);
	}

	// }}}
}

?>
