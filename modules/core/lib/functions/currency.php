<?php

function format_price($amount, $include_currency_sign = true, $opts=array())
{
    if (is_double($amount) == false)
        $amount = strtodouble(trim($amount));

    $thousands = isset($opts['thousands']) ? $opts['thousands'] : ' ';
    
    $amount = myround($amount, 2);

    $negative = $amount < 0 ? true : false;
    
    $strAmount = (string)abs($amount);
    
    if (strpos($strAmount, '.') !== false) {
        $left = substr($strAmount, 0, strpos($strAmount, '.'));
        $right = substr($strAmount, strpos($strAmount, '.')+1);
    } else {
        $left = $strAmount;
        $right = '00';
    }
    
    if (strlen($right) == 1)
        $right = $right . '0';
    
    $a = '';
    for($x=0; $x < strlen($left); $x++) {
        if ($x != 0 && strlen($left) > 3 && (strlen($left) - $x) % 3 === 0) {
            $a .= $thousands;
        }
        
        $a .= $left{$x};
    }
    
    $a .= ',' . $right;
    
    
    if ($negative)
        $a = '-' . $a;
    
    if ($include_currency_sign)
        $a = "€ " . $a;
    
    return $a;
    
//     if ($include_currency_sign) {
//         return "€ " . ($amount < 0 ? '-' : '') . str_replace('-', '', trim(money_format('%!i', $amount)));
//     } else
//         return ($amount < 0 ? '-' : '') . str_replace('-', '', trim(money_format('%!i', $amount)));
}


function format_number($amount) {
    return format_price($amount, false, array('thousands' => '.'));
}


function myround($number, $precision = null)
{
    return round($number, $precision);
    if (is_string($number))
        $number = strtodouble($number);

    $decimal_count = decimalCount($number);

    while ($decimal_count > $precision) {
        $decimal_count --;
        $number = round($number, $decimal_count);
    }

    return $number;
}


function format_percentage($number) {
    $number = strtodouble($number);
    
    return myround($number, 2) . ' %';
}

function decimalCount($fNumber)
{
    $fNumber = floatval($fNumber);

    for ($iDecimals = 0; $fNumber != round($fNumber, $iDecimals); $iDecimals ++);

    return $iDecimals;
}

function strtodouble($str)
{
    $str = trim($str);

    if (strpos($str, ',') !== false && strpos($str, '.') !== false) { // zowel komma's als punten ? => duizendtallen weghalen
        if (strpos($str, ',') < strpos($str, '.')) // , gebruikt als duizendtallen? => weghalen
            $str = str_replace(',', '', $str);
        else // . gebruikt als duizendtallen? => weghalen
            $str = str_replace('.', '', $str);
    }

    $str = str_replace(',', '.', $str);

    $str = preg_replace('/[^\\d\\.\\-]/', '', $str);

    return doubleval($str);
}


/**
 * compare_currency() - compares 2 prices in cents
 * @return
 *  - equal: 0
 *  - price1 > price2: 1
 *  - price1 < price2: -1
 */
function compare_currency($price1, $price2) {
    $v1 = strtodouble($price1);
    $v2 = strtodouble($price2);
    
    return compare_number($v1, $v2, 2);
}

function compare_number($num1, $num2, $decimals=2) {
    $x = pow(10, $decimals);
    
    $num1 = (int) ($num1 * $x);
    $num2 = (int) ($num2 * $x);
    
    return $num1 <=> $num2;
}

function currency_plus($price1, $price2) {
    $p1 = round(strtodouble($price1) * 100);
    $p2 = round(strtodouble($price2) * 100);
    
    return myround(($p1 + $p2)/100, 2);
}

function currency_min($price1, $price2) {
    $p1 = round(strtodouble($price1) * 100);
    $p2 = round(strtodouble($price2) * 100);
    
    return myround(($p1 - $p2)/100, 2);
}


if (function_exists('money_format') == false) {
    function money_format($format, $number)
    {
        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];
            
            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value  *= -1;
            }
            $letter = $positive ? 'p' : 'n';
            
            $prefix = $suffix = $cprefix = $csuffix = $signal = '';
            
            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                $csuffix;
            } else {
                $currency = '';
            }
            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';
            
            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);
            
            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }
            
            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}
