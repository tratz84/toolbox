<?php

namespace codegen;


class PhpCodeParser {
    
    protected $parts;
    
    public function parse($file) {
        $data = file_get_contents( $file );
        
        $this->parts = array();
        
        $this->parseString( $data );
        
//         var_export($this->parts);exit;
   
//         print $this->partsToString();exit;
//         $c = $this->listClasses(); var_export($c); exit;
//         $c = $this->listFunctions(); var_export($c); exit;
        
    }
    

    public function listFunctions($parts=null, $currentClass=null, $state=null) {
        if ($parts === null) {
            $parts = $this->parts;
        }
        if ($state === null) {
            $state = array();
            $state['depth'] = 0;
        } else {
            $state['depth']++;
        }
        
        $funcs = array();
        
        $accoCount = 0;
        $resetClassNameOnAcco = null;
        for($x=0; $x < count($parts); $x++) {
            if ($x+2 < count($parts)) {
                if ($parts[$x]['type'] == 'php' && $parts[$x]['string'] == '{') {
                    $accoCount++;
                }
                if ($parts[$x]['type'] == 'php' && $parts[$x]['string'] == '}') {
                    // outside 'currentClass' ?
                    if ($resetClassNameOnAcco !== null && $accoCount-1 == $resetClassNameOnAcco) {
                        $resetClassNameOnAcco = null;
                        $currentClass = null;
                    }
                    
                    $accoCount--;
                }
                
                if ($parts[$x]['type'] == 'php' && $parts[$x]['string'] == 'class') {
                    $currentClass = $parts[$x+2]['string'];
                    $resetClassNameOnAcco = $accoCount;
                }
                
                
                if ($parts[$x]['type'] == 'php' && $parts[$x]['string'] == 'function') {
                    $funcname = $parts[$x+2]['string'];
                    if (strpos($funcname, '(') !== false)
                        $funcname = substr($funcname, 0, strpos($funcname, '('));
                    
                    // skip anonymous functions
                    if ($funcname) {
                        if ($currentClass) {
                            $funcname = $currentClass . '::' . $funcname;
                        }
                        
                        $funcs[] = $funcname;
                    }
                }
            }
            
            if (isset($parts[$x]['subs']) && count($parts[$x]['subs'])) {
                $fc = $this->listFunctions( $parts[$x]['subs'], $currentClass, $state );
                if (count($fc))
                    $funcs = array_merge($funcs, $fc);
            }
        }
        
        return $funcs;
    }
    
    public function listClasses($parts=null) {
        if ($parts === null) {
            $parts = $this->parts;
        }
        
        $classes = array();
        
        for($x=0; $x < count($parts); $x++) {
            if ($x+2 < count($parts)) {
                if ($parts[$x]['type'] == 'php' && $parts[$x]['string'] == 'class') {
                    $classes[] = $parts[$x+2]['string'];
                }
            }
            
            if (isset($parts[$x]['subs']) && count($parts[$x]['subs'])) {
                $sc = $this->listClasses($parts[$x]['subs']);
                if (count($sc))
                    $classes = array_merge($classes, $sc);
            }
        }
        
        return $classes;
    }
    
    
    protected function partsToString($parts=null) {
        if ($parts === null) {
            $parts = $this->parts;
        }
        
        $str = '';
        
        foreach($parts as $p) {
            $str .= $p['string'];
            
            if (isset($p['subs']) && count($p['subs'])) {
                $str .= $this->partsToString( $p['subs'] );
            }
        }
        
        return $str;
    }
    
    
    public function parseString($str) {
        
        $blocks = $this->stringToBlocks($str);
        
        $escapeChars = array('\\');
        $whiteChars = array(" ", "\t", "\n", "\r", "\i");
        $string = array('"', '\'', '`');
        
        $subPart = array();
        $subPart = array('(', ')');     // start = (, end = )
        
        $state = array();
        $state['depth'] = 0;
        
        
        $parts = array();
        $token = '';
        
        for($x=0; $x < count($blocks); $x++) {
            $block = $blocks[$x];
            
//             var_export($blocks);exit;
            
            $state['pos'] = 0;
            $state['len'] = strlen($block['content']);
            $state['prev'] = null;
            $state['escape'] = false;
            $state['in_string'] = false;
            $state['in_datablock'] = false;
            $state['in_comment'] = false;
            
            if ($block['type'] == 'html') {
                $this->addPart('html', $state, $block['content']);
            }
            
            if ($block['type'] == 'php') {
                $content = $block['content'];
                
                $contentlen = strlen($content);
                $buf = '';
                for($p=0; $p < $contentlen; $p++) {
                    $in_something = $state['in_string'] !== false || $state['in_datablock'] !== false || $state['in_comment'] !== false;
                    
                    $c = $content{$p};
                    if ($c == "\r") continue;
                    
                    if ($in_something == false && in_array($c, $whiteChars) && in_array($state['prev'], $whiteChars) == false) {
                        $this->addPart('php', $state, $buf);
                        $buf = '';
                    }

                    if ($in_something == false && in_array($c, $whiteChars) == false && in_array($state['prev'], $whiteChars) == true) {
                        $this->addPart('php', $state, $buf);
                        $buf = '';
                    }
                    
                    if ($in_something == false && in_array($c, array('{', '}', ';'))) {
                        $buf .= $c;
                        $this->addPart('php', $state, $buf);
                        $buf = '';
                        continue;
                    }
                    
                    if ($in_something == false && $c == '{') {
                        $state['depth']++;
                    }
                    
                    if ($in_something == false && $c == '}') {
                        $state['depth']--;
                    }
                    
                    
                    if ($state['escape']) {
                        $state['escape'] = false;
                    } else if ($c == '\\') {
                        $state['escape'] = true;
                    } else if ($in_something == false && $state['in_string'] == false && ($c == '"' || $c == "'")) {
                        $state['in_string'] = $c;
                    } else if ($state['in_string'] !== false && $state['in_string'] == $c) {
                        $state['in_string'] = false;
                    } else if ($state['in_string'] !== false) {
                        // in string? => just add to buf..
                    } else if ($state['in_comment'] == '//' && $c == "\n") {
                        $state['in_comment'] = false;
                    } else if ($state['in_comment'] == '/*' && $state['prev'] == '*' && $c == '/') {
                        $state['in_comment'] = false;
                    } else if ($state['in_comment'] != false) {
                        // in comment => just add to buf..
                    } else if ($in_something == false && $state['in_comment'] == false && $state['prev'] == '/' && $c == '/') {
                        $state['in_comment'] = '//';
                    } else if ($in_something == false && $state['in_comment'] == false && $state['prev'] == '/' && $c == '*') {
                        $state['in_comment'] = '/*';
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
                    }
                    
                    
                    $state['prev'] = $c;
                    $buf .= $c;
                }
                
                $this->addPart('php', $state, $buf);
            }
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
        $state['in_comment'] = false;
        
        while ($state['pos'] < $state['len']) {
            $c = $str{$state['pos']};
            
            // skip carriage returns
            if ($c == "\r") {
                $state['pos']++;
                continue;
            }
            
            
            if ($state['phpcode']) {
                
                $in_something = $state['in_string'] !== false || $state['in_datablock'] !== false || $state['in_comment'] !== false;
                
                if ($state['escape']) {
                    $state['escape'] = false;
                } else if ($c == '\\') {
                    $state['escape'] = true;
                } else if ($in_something == false && $state['in_string'] == false && ($c == '"' || $c == "'")) {
                    $state['in_string'] = $c;
                } else if ($state['in_string'] !== false && $state['in_string'] == $c) {
                    $state['in_string'] = false;
                } else if ($state['in_string'] !== false) {
                    // in string? => just add to buf..
                } else if ($state['in_comment'] == '//' && $c == "\n") {
                    $state['in_comment'] = false;
                } else if ($state['in_comment'] == '/*' && $state['prev'] == '*' && $c == '/') {
                    $state['in_comment'] = false;
                } else if ($state['in_comment'] != false) {
                    // in comment => just add to buf..
                } else if ($in_something == false && $state['in_comment'] == false && $state['prev'] == '/' && $c == '/') {
                    $state['in_comment'] = '//';
                } else if ($in_something == false && $state['in_comment'] == false && $state['prev'] == '/' && $c == '*') {
                    $state['in_comment'] = '/*';
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
    
    
    
    protected function addPart($type, $state, $token) {
        if ($token == '') return;
        
        $p = &$this->parts;
        
        for($x=0; $x < $state['depth']; $x++) {
            $c = count($p)-1;
            $p = &$p[$c]['subs'];
        }
        
        if ($type == 'php' && trim($token) == '') {
            $type = 'php-white';
        } else if (strpos($token, '//') === 0) {
            $type = 'php-comment';
        } else if (strpos($token, '/*') === 0) {
            $type = 'php-comment';
        }
        
        $p[] = array(
            'type' => $type,
            'string' => $token,
            'subs' => array()
        );
    }
    
    
}
