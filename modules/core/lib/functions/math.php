<?php



/**
 * math_stdev - calc standard deviation
 * 
 * Credits @ https://www.geeksforgeeks.org/php-program-find-standard-deviation-array/
 */
function math_stdev( $values ) {
    $num_of_elements = count($values);
    
    $variance = 0.0;
    
    $average = array_sum($values) / $num_of_elements;
    
    foreach($values as $i) {
        $variance += pow( ($i - $average), 2 );
    }
    
    return sqrt( $variance / $num_of_elements );
}



