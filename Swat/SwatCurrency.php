<?php
// vim: set fdm=marker:

/**
 * Currency Tools
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCurrency
{
    // {{{ public static function format()

	/**
	 * Format a number into a monetary string
	 *
	 * @param float $value The numeric amount to format.
	 * @param string $locale Optional locale to format currency. Set with php
	 *        envionament variables.
	 *
	 * @return string Formatted currency value
	 */
	function format($value, $locale = null)
	{
		if (setlocale(LC_ALL,0) == 'C')
			setlocale(LC_ALL, 'en_US');

		if ($locale !== null) {
			$old_locale = setlocale(LC_ALL, 0);
			setlocale(LC_ALL, $locale);
			$lc = localeconv();
			setlocale(LC_ALL, $old_locale);
		} else
			$lc = localeconv();
	
		$value = abs($value);
		$value = number_format($value, $lc['frac_digits'],
			$lc['mon_decimal_point'], $lc['mon_thousands_sep']);

		$neg = ($value < 0);

		$symbol	      = $lc['currency_symbol'];
		$sign_symbol  = $lc[(($neg) ? 'negative_sign'  : 'positive_sign')];
		$sign_posn    = $lc[(($neg) ? 'n_sign_posn'    : 'p_sign_posn')];
		$cs_precedes  = $lc[(($neg) ? 'n_cs_precedes'  : 'p_cs_precedes')];
		$sep_by_space = $lc[(($neg) ? 'n_sep_by_space' : 'p_sep_by_space')];
	
		if ($sign_posn == 0)
			$value = '('.$value.')';
		elseif ($cs_precedes) {
			if ($sign_posn == 3)
				$symbol = $sign_symbol.$symbol;
			elseif ($sign_posn == 4)
				$symbol.= $sign_symbol;

			$space = ($sep_by_space) ? ' ' : '';
			$value = $symbol.$space.$value;

		} else {
			$space = ($sep_by_space) ? ' ' : '';
			$value.= $space.$symbol;
		}

		if ($sign_posn == 1)
			$value = $sign_symbol.$value;
		elseif ($sign_posn == 2) 
			$value.= $sign_symbol;

		return $value;
	}
	// }}}
}

?>
