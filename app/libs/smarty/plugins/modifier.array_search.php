<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty array_search modifier plugin
 *
 * Type:     modifier<br>
 * Name:     array_search<br>
 * Purpose:  searches the array for a given value and returns the corresponding key if successful
 * @author   Manel Perez
 * @param mixed
 * @param array
 * @return mixed Returns the key for needle if it is found in the array, FALSE otherwise.
 */
function smarty_modifier_array_search( $needle, $haystack )
{
    return array_search( $needle, $haystack );
}

?>
