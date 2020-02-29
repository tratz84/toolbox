<?php


namespace core\parser;


class HtmlParser {
    
    protected $html = null;
    
    protected $parts = null;
    
    
    public function __construct() {
        
        
    }
    
    public function loadString($html) { $this->html = $html; }
    public function loadFile($file) { $this->html = file_get_contents($file); }
    
    
    public function getBlocks() { return $this->blocks; }
    
    public function parse() {
        $blocks = $this->htmlToBlocks();
        
        $this->parts = $this->parseBlocks( $blocks );
        
        var_export( $this->parts );exit;
    }
    
    
    
    
    
    /**
     * parseBlocks() - parse blocks into a tree
     */
    protected function parseBlocks($blocks, $startpos=0) {
        $parts = array();
        
        // var_export($blocks);exit;
        
        for($x=$startpos; $x < count($blocks); $x++) {
            $b = $blocks[$x];
            
            // print "$x\n";
            
            if ($b['type'] == 'html') {
                $trimmed_content = trim($b['content']);
                
                if (strpos($trimmed_content, '</') === 0) {
                    $parts[] = $b;
                    
                    if ($startpos == 0) {
                        return $parts;
                    } else {
                        return array($x, $parts);
                    }
                }
                else if ($b['type'] == 'html' && endsWith($trimmed_content, '/>') == false) {
                    // single-tag elements
                    $tags = ['!', 'meta', 'link', 'img', 'br'];
                    foreach($tags as $tag) {
                        if (stripos($trimmed_content, '<'.$tag) === 0) {
                            $parts[] = $b;
                            continue 2;
                        }
                    }
                    
                    list($pos, $childNodes) = $this->parseBlocks($blocks, $x+1);
                    
                    $x = $pos;
                    
                    $childNodes = array_merge(array($b), $childNodes);
                    $parts[] = array('childNodes' => $childNodes);
                }
            }
            else {
                $parts[] = $b;
            }
        }
    
        if ($startpos == 0) {
            return $parts;
        } else {
            return array($x, $parts);
        }
    }
    
    /**
     * htmlToBlocks() - parse text into text & html parts
     */
    public function htmlToBlocks() {
        $state = array();
        $state['in_tag'] = false;
        $state['in_string'] = false;
        $state['pos'] = 0;
        
        $blocks = array();
        $blocks[] = array('type' => 'text', 'content' => '');
        
        $html_len = strlen($this->html);
        $block_no = 0;
        for($pos=0; $pos < $html_len; $pos++) {
            $char = $this->html{$pos};
            $next_char = null;
            if ($pos+1 < $html_len) {
                $next_char = $this->html{$pos+1};
            }
            // print $char;
            
            if ($state['in_tag'] == false && $char == '<' && in_array($next_char, [" ", "\n", "\t"]) == false) {
                $state['in_tag'] = true;
                
                $block_no++;
                $blocks[] = array('type' => 'html', 'content' => '');
            }
            
            
            if ($state['in_tag'] == true && $state['in_string'] == false && ($char == "'" || $char == '"')) {
                $state['in_string'] = $char;
            }
            else if ($state['in_string'] && $char == $state['in_string']) {
                $state['in_string'] = false;
            }

            $blocks[$block_no]['content'] .= $char;
            
            if ($state['in_string'] == false && $state['in_tag'] == true && $char == '>') {
                $state['in_tag'] = false;
                
                $block_no++;
                $blocks[] = array('type' => 'text', 'content' => '');
            }
        }
        
        $b2 = array();
        foreach($blocks as $b) {
            if ($b['content'] != '') {
                $b2[] = $b;
            }
        }
        
        return $b2;
    }
    
    
}


