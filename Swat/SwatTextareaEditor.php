<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatTextarea.php';

/**
 * A wysiwyg text entry widget
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
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
	public $height = '15em';

	/**
	 * Base-Href
	 *
	 * Optional base-href, used to reference images and other urls in the editor.
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

		$this->addJavaScript(
			'packages/swat/javascript/swat-textarea-editor.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/swat-textarea-editor.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function process()

	public function process()
	{
		parent::process();

		if ($this->value !== null)
			$this->value = str_replace("\n", '', $this->value);
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

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

		$value = $this->rteSafe($this->value);
		$basehref = ($this->basehref === null) ? 'null' : $this->basehref;

		$javascript.=
			"initRTE('swat/images/textarea-editor/', 'swat/', '', false);";

		$javascript.= sprintf("\nwriteRichText('%s', '%s', '%s', '%s', '%s');",
			$this->id,
			$value,
			$this->width,
			$this->height,
			$basehref);

		return $javascript;
	}

	// }}}
	// {{{ protected function getInlineJavaScriptTranslations()

	protected function getInlineJavaScriptTranslations()
	{
		$javascript = 'var rteT = {';

		$translations = $this->getTranslations();
		$count = 0;
		$num_translations = count($translations);
		foreach ($translations as $key => $word) {
			$count++;
			if ($count == $num_translations) {
				$javascript.= sprintf("\n\t%s: '%s'",
					$key, str_replace("'", "\\'", $word));
			} else {
				$javascript.= sprintf("\n\t%s: '%s',",
					$key, str_replace("'", "\\'", $word));
			}
		}

		$javascript.= "\n};\n";

		return $javascript;
	}

	// }}}
	// {{{ private function getTranslations()

	private function getTranslations()
	{
		return array(
			'bold' => Swat::_('Bold'),
			'italic' => Swat::_('Italic'),
			'underline' => Swat::_('Underline'),
			'align_left' => Swat::_('Align Left'),
			'align_right' => Swat::_('Align Right'),
			'align_center' => Swat::_('Align Center'),
			'ordered_list' => Swat::_('Ordered List'),
			'unordered_list' => Swat::_('Unordered List'),
			'indent' => Swat::_('Indent'),
			'outdent' => Swat::_('Outdent'),
			'insert_link' => Swat::_('Insert Link'),
			'horizontal_rule' => Swat::_('Horizontal Rule'),
			'highlight' => Swat::_('Highlight'),
			'quote' => Swat::_('Quote'),
			'style' => Swat::_('Style'),
			'clear_formatting' => Swat::_('Clear Formatting'),
			'paragraph' => Swat::_('Paragraph'),
			'heading' => Swat::_('Heading'),
			'address' => Swat::_('Address'),
			'formatted' => Swat::_('Formatted'),

			//pop-up link
			'enter_url' => Swat::_('A URL is required'),
			'url' => Swat::_('URL'),
			'link_text' => Swat::_('Link Text'),
			'target' => Swat::_('Target'),
			'insert_link' => Swat::_('Insert Link'),
			'cancel' => Swat::_('Cancel')
		);
	}

	// }}}
	// {{{ private function rteSafe()

	private function rteSafe($value)
	{
		//returns safe code for preloading in the RTE

		//convert all types of single quotes
		$value = str_replace(chr(145), chr(39), $value);
		$value = str_replace(chr(146), chr(39), $value);
		$value = str_replace("'", "&#39;", $value);

		//convert all types of double quotes
		$value = str_replace(chr(147), chr(34), $value);
		$value = str_replace(chr(148), chr(34), $value);
		$value = str_replace('"', '&quot;', $value);

		//replace carriage returns & line feeds
		$value = str_replace(chr(10), " ", $value);
		$value = str_replace(chr(13), " ", $value);

		return $value;
	}

	// }}}
}

?>
