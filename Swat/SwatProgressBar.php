<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Progress bar
 *
 * Progress bars should only be used to indicate progress. Use a separate
 * widget to display values.
 *
 * This progress bar is easily scripted using JavaScript. The JavaScript
 * method SwatProgressBar::setValue() sets the value of the progress bar. The
 * JavaScript methods SwatProgressBar::pulse() changes the progress bar into
 * pulse mode. Use pulse mode if progress is happening but there is no way to
 * measure the progress.
 *
 * Both the SwatProgressBar PHP class and SwatProgressBar JavaScript class are
 * accurate to four decimal places. This translates to one-hundredth of a
 * percent.
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatProgressBar extends SwatControl
{
	// {{{ class constants

	/**
	 * Progress bar displays horizontally and completes from left to right
	 */
	const ORIENTATION_LEFT_TO_RIGHT = 1;

	/**
	 * Progress bar displays horizontally and completes from right to left
	 */
	const ORIENTATION_RIGHT_TO_LEFT = 2;

	/**
	 * Progress bar displays vertically and completes from bottom to top
	 */
	const ORIENTATION_BOTTOM_TO_TOP = 3;

	/**
	 * Progress bar displays vertically and completes from top to bottom
	 */
	const ORIENTATION_TOP_TO_BOTTOM = 4;

	// }}}
	// {{{ public properties

	/**
	 * Orientation of this progress bar
	 *
	 * This should be one of the SwatProgressBar::ORIENTATION_* constants. If
	 * an invalid value is used,
	 * {@link SwatProgressBar::ORIENTATION_LEFT_TO_RIGHT} is used.
	 *
	 * @var integer
	 */
	public $orientation = self::ORIENTATION_LEFT_TO_RIGHT;

	/**
	 * The current value of this progress bar
	 *
	 * This should be a value between 0 and 1. If the number is greater than 1
	 * the progress bar will display as 100%. If the number is less than 0, the
	 * progress bar will display as 0%.
	 *
	 * @var float
	 */
	public $value = 0;

	/**
	 * Text to show beneath the progress bar
	 *
	 * Optionally uses vsprintf() syntax, for example:
	 * <code>
	 * $progress_bar->text = '%s%% complete';
	 * </code>
	 *
	 * @var string
	 *
	 * @see SwatProgressBar::$text_value
	 */
	public $text;

	/**
	 * Value or array of values to substitute into the
	 * {@link SwatProgressBar::$text} property
	 *
	 * The value property may be specified either as an array of values or as
	 * a single value. If an array is passed, a call to vsprintf() is done
	 * on the {@link SwatProgressBar::$text} property. If the value is a string
	 * a single sprintf() call is made.
	 *
	 * @var string|array
	 *
	 * @see SwatProgressBar::$text
	 */
	public $text_value = null;

	/**
	 * Optional content type for text of this progress bar
	 *
	 * Defaults to 'text/plain', use 'text/xml' for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	/**
	 * Length of this progress bar in cascading style-sheet units
	 *
	 * This determines the width of horizontal progress bars and the height of
	 * vertical progress bars. Any valid cascading style-sheet dimension
	 * value may be used.
	 *
	 * @var string
	 */
	public $length = '200px';

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new progress bar
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addStyleSheet('packages/swat/styles/swat-progress-bar.css');
		$this->addJavaScript('packages/swat/javascript/swat-progress-bar.js');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this progress bar
	 *
	 * @throws SwatException if this progress bar's <i>$length</i> property is
	 *                       not a valid cascading style-sheet dimension.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();

		$div_tag->open();

		$this->displayBar();
		$this->displayText();

		$div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function displayBar()

	/**
	 * Displays the bar part of this progress bar
	 *
	 * @throws SwatException if this progress bar's <i>$length</i> property is
	 *                       not a valid cascading style-sheet dimension.
	 */
	protected function displayBar()
	{
		// ensure length is in cascading style-sheet units
		$dimension_pattern = '/([0-9]+(%|p[xtc]|e[mx]|in|[cm]m|)|auto)/';
		if (preg_match($dimension_pattern, $this->length) == 0)
			throw new SwatException(sprintf('$length must be specified in '.
				'cascading style-sheet units. Value was: %s', $this->length));

		$bar_div_tag = new SwatHtmlTag('div');
		$bar_div_tag->id = "{$this->id}_bar";
		$bar_div_tag->class = 'swat-progress-bar-bar';

		$full_div_tag = new SwatHtmlTag('div');
		$full_div_tag->id = "{$this->id}_full";
		$full_div_tag->class = 'swat-progress-bar-full';
		$full_div_tag->setContent('');

		$empty_div_tag = new SwatHtmlTag('div');
		$empty_div_tag->id = "{$this->id}_empty";
		$empty_div_tag->class = 'swat-progress-bar-empty';
		$empty_div_tag->setContent('');

		$full_length = min(100, round($this->value * 100, 4));
		$full_length = sprintf('%s%%', $full_length);

		$empty_length = max(0, round(100 - $this->value * 100, 4));
		$empty_length = sprintf('%s%%', $empty_length);

		switch ($this->orientation) {
		case self::ORIENTATION_LEFT_TO_RIGHT:
		default:
			$bar_div_tag->class.= ' swat-progress-bar-left-to-right';
			$bar_div_tag->style = sprintf('width: %s;', $this->length);
			$full_div_tag->style = sprintf('width: %s;', $full_length);
			$empty_div_tag->style = sprintf('width: %s;', $empty_length);
			break;

		case self::ORIENTATION_RIGHT_TO_LEFT:
			$bar_div_tag->class.= ' swat-progress-bar-right-to-left';
			$bar_div_tag->style = sprintf('width: %s;', $this->length);
			$full_div_tag->style = sprintf('width: %s;', $full_length);
			$empty_div_tag->style = sprintf('width: %s;', $empty_length);
			break;

		case self::ORIENTATION_BOTTOM_TO_TOP:
			$bar_div_tag->class.= ' swat-progress-bar-bottom-to-top';
			$bar_div_tag->style = sprintf('height: %s;', $this->length);
			$full_div_tag->style = sprintf('height: %s; top: %s;',
				$full_length, $empty_length);

			$empty_div_tag->style = sprintf('height: %s; top: -%s;',
				$empty_length, $full_length);

			break;

		case self::ORIENTATION_TOP_TO_BOTTOM:
			$bar_div_tag->class.= ' swat-progress-bar-top-to-bottom';
			$bar_div_tag->style = sprintf('height: %s;', $this->length);
			$full_div_tag->style = sprintf('height: %s;', $full_length);
			$empty_div_tag->style = sprintf('height: %s;', $empty_length);
			break;
		}

		$bar_div_tag->open();
		$full_div_tag->display();
		$empty_div_tag->display();
		$bar_div_tag->close();
	}

	// }}}
	// {{{ protected function displayText()

	/**
	 * Displays the text part of this progress bar
	 */
	protected function displayText()
	{
		if ($this->text === null) {
			// still show an empty span if there is no text for this
			// progress bar
			$text = '';
		} else {
			if ($this->text_value === null)
				$text = $this->text;
			elseif (is_array($this->text_value))
				$text = vsprintf($this->text, $this->text_value);
			else
				$text = sprintf($this->text, $this->text_value);
		}

		$span_tag = new SwatHtmlTag('span');
		$span_tag->id = $this->id.'_text';
		$span_tag->class = 'swat-progress-bar-text';
		$span_tag->setContent($text, $this->content_type);
		$span_tag->display();
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets inline JavaScript for this progress bar
	 *
	 * @return string inline JavaScript for this progress bar.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new SwatProgressBar('%s', %s, %s);",
			$this->id, $this->id, $this->orientation, $this->value);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this progress bar
	 *
	 * @return array the array of CSS classes that are applied to this progress
	 *                bar.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-progress-bar');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
