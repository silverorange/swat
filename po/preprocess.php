<?php

/**
 * Removes PHP string concatenations inside gettext markers given a PHP source
 * file.
 *
 * @copyright 2006-2016 silverorange
 */
$source = file_get_contents($_SERVER['argv'][1]);

$output = '';
$in_gettext = false;
$in_gettext_string = false;
$gettext_blank_lines = 0;

$tokens = token_get_all($source);
for ($i = 0; $i < count($tokens); $i++) {
    $token = $tokens[$i];
    if (is_string($token)) {
        switch ($token) {
            // ignore string concatenation inside gettext calls
            case '.':
                if (!$in_gettext) {
                    $output .= $token;
                }

                break;

                // for ngettext multiple translations
            case ',':
                if ($in_gettext_string) {
                    $output .= "', ";
                    $in_gettext_string = false;
                } else {
                    $output .= $token;
                }
                break;

                // TODO: this can catch ngettext('string1', 'string2', count($foo))
                //                              ^                                ^
            case ')':
                if ($in_gettext) {
                    if ($in_gettext_string) {
                        $output .= "'";
                        $in_gettext_string = false;
                    }
                    $output .= $token;
                    // blank lines so line numbers match with original file
                    $output .= str_repeat("\n", $gettext_blank_lines);
                    $in_gettext = false;
                } else {
                    $output .= $token;
                }

                break;

            default:
                $output .= $token;
                break;
        }
    } else {
        [$id, $text] = $token;
        switch ($id) {
            // most whitespace in gettext is from concatenation, ignore it
            case T_WHITESPACE:
                if ($in_gettext) {
                    if (strpos($text, "\n") !== false) {
                        $gettext_blank_lines++;
                    }
                } else {
                    $output .= $text;
                }
                break;

            case T_STRING:
                $output .= $text;
                if ($text === '_' || $text === 'ngettext' || $text === 'gettext') {
                    if ($in_gettext) {
                        echo 'error: gettext marker detected inside gettext '
                            . "marker\n";

                        exit(1);
                    }

                    // ignore gettext function definition
                    if ($tokens[$i - 2][0] != T_FUNCTION) {
                        $in_gettext = true;
                        $gettext_blank_lines = 0;
                    }
                }
                break;

            case T_CONSTANT_ENCAPSED_STRING:
                if ($in_gettext) {
                    if (!$in_gettext_string) {
                        $output .= "'";
                        $in_gettext_string = true;
                    }

                    // normalize to single quotes
                    $string = $text;
                    if ($string[0] === '"') {
                        $string = str_replace('\"', '"', $string);
                    }

                    if ($string[0] === "'") {
                        $string = str_replace("\\'", "'", $string);
                    }

                    $string = substr($string, 1, -1);
                    $string = str_replace("'", "\\'", $string);
                    $output .= $string;
                } else {
                    $output .= $text;
                }
                break;

            default:
                $output .= $text;
                break;
        }
    }
}

echo $output;
