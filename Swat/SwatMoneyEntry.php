<?php

/**
 * A money entry widget.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatMoneyEntry extends SwatFloatEntry
{
    /**
     * Optional locale for currency format.
     *
     * If no locale is specified, the current system locale is used.
     *
     * @var string
     */
    public $locale;

    /**
     * Whether to display international currency symbol.
     *
     * If true, displays the international currency symbol after the input box.
     *
     * @var bool
     */
    public $display_currency = false;

    /**
     * Number of decimal places to accept.
     *
     * This also controls how many decimal places are displayed when editing
     * existing values.
     *
     * If set to null, the number of decimal places allowed by the locale is
     * used.
     *
     * @var ?int
     */
    public $decimal_places;

    /**
     * Displays this money entry widget.
     *
     * The widget is displayed as an input box and an optional currency symbol.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        if ($this->display_currency) {
            $locale = SwatI18NLocale::get($this->locale);
            echo SwatString::minimizeEntities(
                ' ' . $locale->getInternationalCurrencySymbol(),
            );
        }
    }

    /**
     * Processes this money entry widget.
     *
     * If the value of this widget is not a monetary value or the number of
     * fractional decimal places is not within the allowed range, an error
     * message is attached to this money entry widget.
     */
    public function process()
    {
        parent::process();

        if ($this->value === null) {
            return;
        }

        $locale = SwatI18NLocale::get($this->locale);
        $format = $locale->getNationalCurrencyFormat();
        $max_decimal_places =
            $this->decimal_places ?? $format->fractional_digits;

        $value = $this->getRawValue();

        // Get the number of entered fractional digits places from the raw
        // value. This checks the raw value instead of the processed value
        // because the processed value could have been parsed into a float by
        // this point.
        $decimal_position = mb_strpos(
            (string) $value,
            $format->decimal_separator,
        );
        if ($decimal_position === false) {
            $decimal_places = 0;
        } else {
            $fractional_digits = mb_substr(
                rtrim((string) $value, '0'),
                $decimal_position + mb_strlen($format->decimal_separator),
            );

            $decimal_places = preg_match_all('/[0-9]/', $fractional_digits);
        }

        // check if length of the given fractional part is more than the
        // allowed length
        if ($decimal_places > $max_decimal_places) {
            // validation failed so reset value to the raw value
            $this->value = $value;

            if ($this->decimal_places === null) {
                $message = $this->getValidationMessage(
                    'currency-decimal-places',
                );

                $max_decimal_places_formatted = str_replace(
                    '%',
                    '%%',
                    $locale->formatNumber($max_decimal_places),
                );

                // C99 specification includes spacing character, remove it
                $currency_formatted = str_replace(
                    '%',
                    '%%',
                    $locale->getInternationalCurrencySymbol(),
                );

                $message->primary_content = sprintf(
                    $message->primary_content,
                    $currency_formatted,
                    $max_decimal_places_formatted,
                );
            } else {
                if ($max_decimal_places === 0) {
                    $message = $this->getValidationMessage('no-decimal-places');
                } else {
                    $max_decimal_places_formatted = str_replace(
                        '%',
                        '%%',
                        $locale->formatNumber($max_decimal_places),
                    );

                    // note: not using getValidationMessage() because of
                    // ngettext. We may want to add this ability to that method
                    $message = new SwatMessage(
                        sprintf(
                            Swat::ngettext(
                                'The %%s field has too many decimal places. There ' .
                                    'can be at most one decimal place.',
                                'The %%s field has too many decimal places. There ' .
                                    'can be at most %s decimal places.',
                                $max_decimal_places,
                            ),
                            $max_decimal_places_formatted,
                        ),
                        'error',
                    );
                }
            }

            $this->addMessage($message);
        }
    }

    /**
     * Formats a monetary value to display.
     *
     * @param string $value the value to format for display
     *
     * @return string the formatted value
     */
    protected function getDisplayValue($value)
    {
        // if the value is valid, format accordingly
        if (!$this->hasMessage() && is_numeric($value)) {
            $value = SwatI18NLocale::get($this->locale)->formatCurrency(
                $value,
                false,
                ['fractional_digits' => $this->decimal_places],
            );
        }

        return $value;
    }

    /**
     * Gets the numeric value of this money entry.
     *
     * @param string $value the raw value to use to get the numeric value
     *
     * @return mixed the numeric value of this money entry widget or null if no
     *               numeric value is available
     */
    protected function getNumericValue($value)
    {
        return SwatI18NLocale::get($this->locale)->parseCurrency($value);
    }

    /**
     * Gets a validation message for this money entry widget.
     *
     * @see SwatEntry::getValidationMessage()
     *
     * @param string $id the string identifier of the validation message
     *
     * @return SwatMessage the validation message
     */
    protected function getValidationMessage($id)
    {
        switch ($id) {
            case 'float':
                $locale = SwatI18NLocale::get($this->locale);
                $currency = $locale->getInternationalCurrencySymbol();
                $example = $locale->formatCurrency(1036.95, false, [
                    'fractional_digits' => $this->decimal_places,
                ]);

                $text = sprintf(
                    $this->show_field_title_in_messages
                        ? Swat::_(
                            'The %%s field must be a monetary value ' .
                                'formatted for %s (i.e. %s).',
                        )
                        : Swat::_(
                            'This field must be a monetary value ' .
                                'formatted for %s (i.e. %s).',
                        ),
                    str_replace('%', '%%', $currency),
                    str_replace('%', '%%', $example),
                );

                $message = new SwatMessage($text, 'error');
                break;

            case 'currency-decimal-places':
                $text = $this->show_field_title_in_messages
                    ? Swat::_(
                        'The %%s field has too many decimal places. The ' .
                            'currency %s only allows %s.',
                    )
                    : Swat::_(
                        'This field has too many decimal places. The ' .
                            'currency %s only allows %s.',
                    );

                $message = new SwatMessage($text, 'error');
                break;

            case 'no-decimal-places':
                $text = $this->show_field_title_in_messages
                    ? Swat::_('The %s field must not have any decimal places.')
                    : Swat::_('This field must not have any decimal places.');

                $message = new SwatMessage($text, 'error');
                break;

            default:
                $message = parent::getValidationMessage($id);
                break;
        }

        return $message;
    }

    /**
     * Gets the array of CSS classes that are applied to this entry.
     *
     * @return array the array of CSS classes that are applied to this
     *               entry
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-money-entry'];

        return array_merge($classes, parent::getCSSClassNames());
    }
}
