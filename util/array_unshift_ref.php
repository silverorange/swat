<?php
/**
 * http://ca.php.net/manual/en/function.array-unshift.php
 *
 * @return int
 * @param $array array
 * @param $value mixed
 * @desc Prepend a reference to an element to the beginning of an array. Renumbers numeric keys, so $value is always inserted to $array[0]
 */

function array_unshift_ref(&$array, &$value) {
	$return = array_unshift($array,'');
	$array[0] =& $value;
	return $return;
}
?>
