<?php


namespace core\parser;

use core\exception\InvalidStateException;


class HtmlParser {
    
    protected $html = null;
    
    protected $parts = null;
    
    
    public function __construct() {
        
        
    }
    
    public function loadString($html) { $this->html = $html; }
    public function loadFile($file) { $this->html = file_get_contents($file); }
    
    
    public function getParts() { return $this->parts; }
    
    public function parse() {
        $tokens = $this->htmlToTokens();
        
        $this->parts = $this->parseTokens( $tokens );
        
    }
    
    public function getBodyText() {
        return $this->getElementText('body');
    }
    
    public function getElementText($elementName) {
        $el = $this->findElement(['element' => $elementName]);
        
        $t = $this->parts2text( $el['childNodes'] );
        
        $t = html_entity_decode($t, ENT_COMPAT, 'UTF-8');
        $t = str_replace("\r", "", $t);
        $t = mb_trim($t, "\n");
        
        return $t;
    }
    
    public function parts2text($parts, $depth=0) {
        if (!$parts) {
            return '';
        }
        
        $text='';
        
        for($x=0; $x < count($parts); $x++) {
            if (@$parts[$x]['tag'] == 'style') {
                continue;
            }
            
            if (isset($parts[$x]['childNodes'])) {
                $text .= $this->parts2text($parts[$x]['childNodes'], $depth+1);
            }
            else if (@$parts[$x]['type'] == 'text') {
                $text .= $parts[$x]['content'];
            }
        }
        
        // $text = preg_replace('/[\r\n]+/', "\n", $text);
        // $text = preg_replace('/^\\s+/u', '', $text);
        if ($depth == 0) {
            $text = preg_replace('/(\n)[\t ]+/u', "\\1", $text);
            $text = preg_replace('/[\n\r]{3,}/u', "\n\n\n", $text);
        }
        
        
        return $text;
    }
    
    public function findElement($filter=array(), $parts=null) {
        if ($parts == null) {
            if ($this->parts === null) {
                throw new InvalidStateException('HtmlParser::parse() not called');
            }
            
            $parts = $this->parts;
        }
        
        for($x=0; $x < count($parts); $x++) {
            if (isset($parts[$x]['childNodes'])) {
                $e = $this->findElement($filter, $parts[$x]['childNodes']);
                if ($e) {
                    return $e;
                }
            }
            
            if (@$parts[$x]['tag'] == @$filter['element']) {
                return $parts[$x];
            }
        }
        
        return null;
    }
    
    
    
    
    /**
     * parseTokens() - parse tokens into a tree
     */
    protected function parseTokens($tokens, $startpos=0) {
        $parts = array();
        
        // var_export($tokens);exit;
        
        for($x=$startpos; $x < count($tokens); $x++) {
            $t = $tokens[$x];
            
            // print "$x\n";
            
            if ($t['type'] == 'html') {
                $trimmed_content = trim($t['content']);
                
                if (strpos($trimmed_content, '</') === 0) {
                    $parts[] = $t;
                    
                    if ($startpos == 0) {
                        return $parts;
                    } else {
                        return array($x, $parts);
                    }
                }
                else if ($t['type'] == 'html' && endsWith($trimmed_content, '/>') == false) {
                    // single-tag elements
                    $tags = ['!', 'meta', 'link', 'img', 'br'];
                    foreach($tags as $tag) {
                        if (stripos($trimmed_content, '<'.$tag) === 0) {
                            $parts[] = $t;
                            continue 2;
                        }
                    }
                    
                    list($pos, $childNodes) = $this->parseTokens($tokens, $x+1);
                    
                    $x = $pos;
                    
                    // get tag name
                    $tag = '';
                    for($y=1; $y < strlen($trimmed_content); $y++) {
                        $yc = $trimmed_content[$y];
                        if ($yc == "\t" || $yc == "\n" || $yc == " " || $yc == ">")
                            break;
                        $tag .= $yc;
                    }
                    
                    // set parts
                    $childNodes = array_merge(array($t), $childNodes);
                    $parts[] = array('tag' => $tag, 'childNodes' => $childNodes);
                }
            }
            else {
                $parts[] = $t;
            }
        }
    
        if ($startpos == 0) {
            return $parts;
        } else {
            return array($x, $parts);
        }
    }
    
    /**
     * htmlToTokens() - parse text into text & html parts
     */
    public function htmlToTokens() {
        $state = array();
        $state['in_tag'] = false;
        $state['in_string'] = false;
        $state['pos'] = 0;
        
        $tokens = array();
        $tokens[] = array('type' => 'text', 'content' => '');
        
        $html_len = strlen($this->html);
        $token_no = 0;
        for($pos=0; $pos < $html_len; $pos++) {
            $char = $this->html[$pos];
            $next_char = null;
            if ($pos+1 < $html_len) {
                $next_char = $this->html[$pos+1];
            }
            // print $char;
            
            if ($state['in_tag'] == false && $char == '<' && in_array($next_char, [" ", "\n", "\t"]) == false) {
                $state['in_tag'] = true;
                
                $token_no++;
                $tokens[] = array('type' => 'html', 'content' => '');
            }
            
            
            if ($state['in_tag'] == true && $state['in_string'] == false && ($char == "'" || $char == '"')) {
                $state['in_string'] = $char;
            }
            else if ($state['in_string'] && $char == $state['in_string']) {
                $state['in_string'] = false;
            }

            $tokens[$token_no]['content'] .= $char;
            
            if ($state['in_string'] == false && $state['in_tag'] == true && $char == '>') {
                $state['in_tag'] = false;
                
                $token_no++;
                $tokens[] = array('type' => 'text', 'content' => '');
            }
        }
        
        $ret_tokens = array();
        foreach($tokens as $t) {
            if ($t['content'] != '') {
                $ret_tokens[] = $t;
            }
        }
        
        return $ret_tokens;
    }
    
    
}


