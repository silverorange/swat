<?php

/**
 * Information for formatting numeric values
 *
 * @package   SwatI18N
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatLocale::formatNumber()
 * @see       SwatLocale::getNumberFormat()
 */
class SwatI18NNumberFormat extends SwatObject
{


    /**
     * Decimal point character
     *
     * @var string
     */
    public $decimal_separator;

    /**
     * Thousands separator
     *
     * @var string
     */
    public $thousands_separator;

    /**
     * Numeric groupings
     *
     * @var array
     */
    public $grouping;



    /**
     * Gets a new number format object with certain properties overridden from
     * specified values
     *
     * The override information is specified as an associative array with
     * array keys representing property names of this formatting object and
     * array values being the overridden values.
     *
     * For example, to override the positive and negative signs of this format,
     * use:
     * <code>
     * <?php
     * $format->override(array('n_sign' => 'neg', 'p_sign' => 'pos'));
     * ?>
     * </code>
     *
     * @param array $format the format information with which to override thss
     *                       format.
     *
     * @return SwatI18NNumberFormat a copy of this number format with the
     *                               specified properties set to the new values.
     *
     * @throws SwatException if any of the array keys do not match a formatting
     *                       property of this property.
     */
    public function override(array $format)
    {
        $vars = get_object_vars($this);

        foreach ($format as $key => $value) {
            if (!array_key_exists($key, $vars)) {
                throw new SwatException(
                    'Number formatting information ' .
                        "contains invalid property {$key} and cannot override " .
                        'this number format.',
                );
            }
        }

        $new_format = clone $this;

        foreach ($format as $key => $value) {
            if ($value !== null) {
                $new_format->$key = $value;
            }
        }

        return $new_format;
    }



    /**
     * Gets a string representation of this format
     *
     * @return string a string representation of this format.
     */
    public function __toString(): string
    {
        $string = '';

        $string .= 'decimal_separator => ' . $this->decimal_separator . "\n";

        $string .=
            'thousands_separator => ' . $this->thousands_separator . "\n";

        $string .= 'grouping => ';
        $string .= is_array($this->grouping)
            ? implode(', ', $this->grouping)
            : $this->grouping;

        $string .= "\n";

        return $string;
    }

}
