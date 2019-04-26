<?php


// credits @ http://apigen.juzna.cz/doc/basdenooijer/solarium/source-class-Solarium.Core.Query.Helper.html

function solr_escapeTerm($str) {
    $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\/|\\\)/';
    
    return preg_replace($pattern, '\\\$1', $str);
}

function solr_escapePhrase($str) {
    return '"' . preg_replace('/("|\\\)/', '\\\$1', $str) . '"';
}


