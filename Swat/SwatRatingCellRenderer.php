<?php

/**
 * A rating cell renderer
 *
 * @package   Swat
 * @copyright 2010-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRatingCellRenderer extends SwatNumericCellRenderer
{
	// {{{ constants

	const ROUND_FLOOR = 1;
	const ROUND_CEIL  = 2;
	const ROUND_UP    = 3;
	const ROUND_NONE  = 4;
	const ROUND_HALF  = 5;

	// }}}
	// {{{ public properties

	/**
	 * Maximum value a rating can be.
	 *
	 * @var integer
	 */
	public $maximum_value = 5;

	/**
	 * Number of digits to display after the decimal point
	 *
	 * If null, the native number of digits displayed by PHP is used. The native
	 * number of digits could be a relatively large number of digits for uneven
	 * fractions.
	 *
	 * @var integer
	 */
	public $round_mode = self::ROUND_FLOOR;

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		SwatCellRenderer::render();

		if ($this->value === null && $this->null_display_value !== null) {
			$this->renderNullValue();
		} elseif ($this->value !== null) {
			$locale = SwatI18NLocale::get();

			$value      = $this->getDisplayValue();
			$difference = $this->maximum_value - $value;

			$rating_class = floor(10 * min($value, $this->maximum_value));
			$rating_class = 'rating-'.$rating_class;

			$outer_span = new SwatHtmlTag('span');
			$outer_span->class = 'rating '.$rating_class;
			$outer_span->open();

			$content = str_repeat('★', ceil($value));
			if ($difference > 0) {
				$content.= str_repeat('☆', floor($difference));
			}

			$value_tag = new SwatHtmlTag('span');
			$value_tag->setContent($content);
			$value_tag->class = 'value';
			$value_tag->title = $locale->formatNumber($value, 1);
			$value_tag->display();

			$best_tag = new SwatHtmlTag('span');
			$best_tag->class = 'best';
			$best_tag->title = $locale->formatNumber($this->maximum_value, 1);
			$best_tag->setContent('');
			$best_tag->display();

			$outer_span->close();
		}
	}

	// }}}
	// {{{ protected function getDisplayValue()

	public function getDisplayValue()
	{
		switch ($this->round_mode) {
		case self::ROUND_FLOOR:
			$value = floor($this->value);
			break;

		case self::ROUND_CEIL:
			$value = ceil($this->value);
			break;

		case self::ROUND_UP:
			$value = round($this->value, $this->precision);
			break;

		case self::ROUND_NONE:
			$value = $this->value;
			break;

		case self::ROUND_HALF:
			$value = round($this->value * 2) / 2;
			break;
		}

		return $value;
	}

	// }}}
}

?>
