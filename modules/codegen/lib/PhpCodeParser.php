<?php

namespace codegen;


class PhpCodeParser {
    
    protected $parts;
    
    public function parse($file) {
        $data = file_get_contents( $file );
        
        $this->parts = array();
        
        $this->parseString( $data );
    }
    
    
    public function parseString($str) {
        
        $blocks = $this->stringToBlocks($str);
        var_export($blocks);exit;
        
        $x=0;
        $len = strlen($sql);
        
        $escapeChars = array('\\');
        $whiteChars = array(" ", "\t", "\n", "\r", "\i");
        $string = array('"', '\'', '`');
        
        $subPart = array();
        $subPart = array('(', ')');     // start = (, end = )
        
        $state = array();
        $state['pos'] = 0;
        $state['escape'] = false;
        $state['in_string'] = false;
        $state['in_comment'] = false;
        $state['prev_char'] = null;
        $state['depth'] = 0;
        
        
        $parts = array();
        $token = '';
        
        while($state['pos'] < $len) {
            $p = $state['pos'];         // current pos`
            $c = $sql{$p};              // current character
            
            // ignore carriage returns
            if ($c == '\r') {
                $state['pos']++;
                continue;
            }
            
            if ($state['in_comment']) {
                $token .= $c;
                if ($c == "\n") {
                    $this->addPart($state, $token);
                    $token = '';
                    $state['in_comment'] = false;
                }
                
                $state['pos']++;
                $state['prev_char'] = $c;
                
                continue;
            }
            
            // escaped?
            if ($state['escape']) {
                $token .= $c;
                $state['pos']++;
                $state['prev_char'] = $c;
                
                $state['escape'] = false;
                continue;
            }
            
            // handle escaping
            if (!$state['escape'] && $c == '\\') {
                $state['pos']++;
                $state['escape'] = true;
                continue;
            } else {
                $state['escape'] = false;
            }
            
            
            // string started?
            if ($state['in_string'] == false && ($c == '\'' || $c == '"')) {
                $state['in_string'] = $c;
                
                $this->addPart($state, $token);
                $token = '';
                
                $token .= $c;
                $state['pos']++;
                $state['prev_char'] = $c;
                continue;
            }
            
            // in string? => handle & check for end..
            if ($state['in_string']) {
                $token .= $c;
                $state['pos']++;
                $state['prev_char'] = $c;
                
                if ($c == $state['in_string']) {
                    $state['in_string'] = false;
                    
                    $this->addPart($state, $token);
                    $token = '';
                }
                
                continue;
            }
            
            
            if (in_array($c, $whiteChars) && in_array($state['prev_char'], $whiteChars) == false) {
                $this->addPart($state, $token);
                
                $token = '';
            }
            
            if (in_array($c, $whiteChars) == false && in_array($state['prev_char'], $whiteChars)) {
                $this->addPart($state, $token);
                
                $token = '';
            }
            
            if ($c == '(') {
                $state['depth']++;
                $token .= $c;
                
                $this->addPart($state, $token);
                
                $token = '';
                $state['pos']++;
                $state['prev_char'] = $c;
                continue;
            }
            if ($c == ')') {
                $token .= $c;
                
                $this->addPart($state, $token);
                $state['depth']--;
                
                $token = '';
                $state['pos']++;
                $state['prev_char'] = $c;
                continue;
            }
            
            if ($c == '#' || $c.$state['prev_char'] == '--') {
                $state['in_comment'] = true;
            }
            
            $token .= $c;
            $state['pos']++;
            $state['prev_char'] = $c;
        }
        
        if ($token) {
            $this->addPart($state, $token);
        }

    }
    
    
    /**
     * stringToBlocks() - parses string to html- & php-blocks
     */
    protected function stringToBlocks($str) {
        $buf = '';
        
        $state = array();
        $state['pos'] = 0;
        $state['len'] = strlen($str);
        $state['phpcode'] = false;
        $state['prev'] = null;
        $state['escape'] = false;
        $state['in_string'] = false;
        $state['in_datablock'] = false;
        
        while ($state['pos'] < $state['len']) {
            $c = $str{$state['pos']};
            
            // skip carriage returns
            if ($c == "\r") {
                $state['pos']++;
                continue;
            }
            
            
            if ($state['phpcode']) {
                
                if ($state['escape']) {
                    $state['escape'] = false;
                } else if ($c == '\\') {
                    $state['escape'] = true;
                } else if ($state['in_string'] == false && ($c == '"' || $c == "'")) {
                    $state['in_string'] = $c;
                } else if ($state['in_string'] !== false && $state['in_string'] == $c) {
                    $state['in_string'] = false;
                } else if ($state['in_string'] !== false) {
                    // in string? => just add to buf..
                } else if ($state['in_datablock'] !== false) {
                    // in_datablock? => check end
                    
                    if ($c == "\n") {
                        $lastline = substr($buf, strrpos($buf, "\n")+1);
                        
                        // check if lastline doesn't start with a whitespace
                        if ($lastline{0} != ' ' && $lastline{0} != "\t") {
                            // spaces/tabs are ignored
                            $lastline = str_replace(array(' ', "\t"), '', $lastline);
                            
                            // check for code end of datablock
                            if ($lastline == $state['in_datablock'].';') {
                                $state['in_datablock'] = false;
                            }
                        }
                    }
                    
                } else if ($c == "\n") {
                    $buflen = strlen($buf);
                    
                    $lastline = null;
                    $lastPosEnter = strrpos($buf, "\n");
                    if ($lastPosEnter !== false) {
                        $lastline = substr($buf, $lastPosEnter+1);
                    } else {
                        $lastline = $buf;
                    }
                    
                    $lastline = str_replace(array(' ', "\t"), '', $lastline);
                    $blocknamepos = strpos($lastline, '<<<');
                    if ($blocknamepos !== false) {
                        $state['in_datablock'] = substr($lastline, $blocknamepos+3);
                    }
                } else if ($state['prev'] == '?' && $c == '>') {
                    $state['phpcode'] = false;
                    $buf .= $c;
                    
                    $blocks[] = array(
                        'type' => 'php',
                        'content' => $buf,
                    );
                    
                    $state['pos']++;
                    $state['prev'] = null;
                    $buf = '';
                    continue;
                }
                
            } else {
                if ($state['prev'] == '<' && $c == '?') {
                    $state['phpcode'] = true;
                    $buf = substr($buf, 0, -1);
                    
                    $blocks[] = array(
                        'type' => 'html',
                        'content' => $buf
                    );
                    
                    $state['pos']++;
                    $state['prev'] = null;
                    $buf = '<?';
                    continue;
                }
            }
            
            $buf .= $c;
            $state['prev'] = $c;
            $state['pos']++;
        }
        
        if ($buf != '') {
            $blocks[] = array(
                'type' => $state['phpcode'] ? 'php' : 'html',
                'content' => $buf,
            );
        }
        
        return $blocks;
    }
    
    
    
    protected function addPart($state, $token) {
        $p = &$this->parts;
        
        for($x=0; $x < $state['depth']; $x++) {
            $c = count($p)-1;
            $p = &$p[$c]['subs'];
        }
        
        $p[] = array(
            'type' => trim($token) == '' ? 'space' : 'string',
            'string' => $token,
            'subs' => array()
        );
    }
    
    
}
