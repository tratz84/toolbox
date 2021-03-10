<?php

namespace core\parser;


class SqlQueryParser {
    
    protected $parts;
    
    protected $splitQueries = array();
    
    protected $queryCommands = array(
        array('cmd' =>'select'),
        array('cmd' =>'from'),
        array('cmd' => array('left join', 'right join', 'join'), 'multi' => true),
        array('cmd' =>'partition'),
        array('cmd' =>'where'),
        array('cmd' =>'group by'),
        array('cmd' =>'having'),
        array('cmd' =>'window'),
        array('cmd' => 'order by'),
        array('cmd' => 'limit'),
        array('cmd' => 'into outfile'),
        array('cmd' => 'for update'),
        array('cmd' => 'for share'),
        array('cmd' => 'union')
    );
    
    
    public function __construct() {
        
    }
    
    
    public function parseQuery($sql) {
        $this->parseString($sql);
        
        $this->splitQueries();
    }
    
    
    public function toString() {
        $sql = '';
        
        for($x=0; $x < count($this->splitQueries); $x++) {
            if ($x > 0) {
                $sql = rtrim($sql) . "\nUNION\n";
            }
            
            $sq = $this->splitQueries[$x];
            
            for($y=0; $y < count($sq); $y++) {
                $sql .= $this->partsToString( $sq[$y]['parts'] );
            }
        }
        
        return $sql;
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
    
    
    public function queryCount() { return count($this->splitQueries); }
    
    public function addWhere($str, $queryNo=0) {
        
        $sq = $this->splitQueries[$queryNo];
        
        // lookup 'where' position
        $whereno = -1;
        for($x=0; $x < count($sq); $x++) {
            if ($sq[$x]['cmd'] == 'where') {
                $whereno = $x;
                break;
            }
        }
        
        $whereInserted = false;
        
        // not existing?
        if ($whereno == -1) {
            $whereInserted = true;
            
            $where_commandno = $this->lookupCommandNo('where');
            
            // loop through commands and find right position to add 'where'
            $newsq = array();
            $whereSet = false;
            for($x=0; $x < count($sq); $x++) {
                // right location to add 'where' ?
                $no = $this->lookupCommandNo( $sq[$x]['cmd'] );
                if ($no > $where_commandno && $whereSet == false) {
                    $newsq[] = array(
                        'cmd' => 'where',
                        'parts' => array()
                    );
                    $whereSet = true;
                    $whereno = $x;
                }
                
                $newsq[] = $sq[$x];
            }

            // location not found in loop? => add 'where' at the end
            if ($whereSet == false) {
                $newsq[] = array(
                    'cmd' => 'where',
                    'parts' => array()
                );
                $whereno = count($newsq)-1;
            }
            
            $this->splitQueries[$queryNo] = $newsq;
            
            // add newline to previous part
            $prevEl = $this->splitQueries[$queryNo][$whereno-1]['parts'];
            if (strpos($prevEl[count($prevEl)-1]['string'], "\n") === false) {
                $this->splitQueries[$queryNo][$whereno-1]['parts'][] = array(
                    'type' => 'space',
                    'string' => "\n",
                    'subs' => array()
                );
            }
            
            // add where
            $this->splitQueries[$queryNo][$whereno]['parts'][] = array(
                'type' => 'string',
                'string' => 'where',
                'subs' => array()
            );
            
            $this->splitQueries[$queryNo][$whereno]['parts'][] = array(
                'type' => 'space',
                'string' => ' ',
                'subs' => array()
            );
            
        }
        
        
        // put parentheses around original where-clause
        if (isset($this->splitQueries[$queryNo][$whereno]['parentheses_set']) == false) {
            $this->splitQueries[$queryNo][$whereno]['parentheses_set'] = true;

            $parts = $this->splitQueries[$queryNo][$whereno]['parts'];
            
            
            if (count($parts) > 2) {
                $pleft = array(
                    'type' => 'string',
                    'string' => ' (',
                    'subs' => array()
                );
                $pright = array(
                    'type' => 'string',
                    'string' => ') ',
                    'subs' => array()
                );
                
                $whereconditions = array_splice($parts, 1);
                
                $parts = array_merge(array($parts[0]), array($pleft), $whereconditions, array($pright));
            }
            
            $this->splitQueries[$queryNo][$whereno]['parts'] = $parts;
        }
        
        // WHERE not added (already a clause?) => prepend with 'AND'
        if ($whereInserted == false) {
            $str = ' AND ' . $str;
        }
        
        $this->splitQueries[$queryNo][$whereno]['parts'][] = array(
            'type' => 'string',
            'string' => $str . "\n",
            'subs' => array()
        );
        
    }
    
    
    public function setOrderBy( $str ) {
        for($qn=0; $qn < count($this->splitQueries); $qn++) {
            $nsq = array();
            for($x=0; $x < count($this->splitQueries[$qn]); $x++) {
                if ($this->splitQueries[$qn][$x]['cmd'] == 'order by') continue;
                
                $nsq[] = $this->splitQueries[$qn][$x];
            }
            $nsq[] = array(
                'cmd' => 'order by',
                'parts' => array(
                    ['string' => 'order by '.$str],
                )
            );
            
            $this->splitQueries[$qn] = $nsq;
        }
        
    }
    
    
    public function removeLimit() {
        for($qn=0; $qn < count($this->splitQueries); $qn++) {
            $nsq = array();
            for($x=0; $x < count($this->splitQueries[$qn]); $x++) {
                if ($this->splitQueries[$qn][$x]['cmd'] == 'limit') continue;
                
                $nsq[] = $this->splitQueries[$qn][$x];
            }
            
            $this->splitQueries[$qn] = $nsq;
        }
    }
    
    
    /**
     * lookupCommandNo() - returns index of command
     */
    protected function lookupCommandNo($cmdname) {
        for($x=0; $x < count($this->queryCommands); $x++) {
            if ($this->queryCommands[$x]['cmd'] == strtolower($cmdname)) {
                return $x;
            }
        }
        return -1;
    }
    
    
    /**
     * parseString() - parses sql query
     * 
     * specs @ https://dev.mysql.com/doc/refman/8.0/en/select.html
     * SELECT
     *    [ALL | DISTINCT | DISTINCTROW ]
     *      [HIGH_PRIORITY]
     *      [STRAIGHT_JOIN]
     *      [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
     *      [SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
     *    select_expr [, select_expr ...]
     *    [FROM table_references
     *      [PARTITION partition_list]
     *    [WHERE where_condition]
     *    [GROUP BY {col_name | expr | position}, ... [WITH ROLLUP]]
     *    [HAVING where_condition]
     *    [WINDOW window_name AS (window_spec)
     *        [, window_name AS (window_spec)] ...]
     *    [ORDER BY {col_name | expr | position}
     *      [ASC | DESC], ... [WITH ROLLUP]]
     *    [LIMIT {[offset,] row_count | row_count OFFSET offset}]
     *    [INTO OUTFILE 'file_name'
     *        [CHARACTER SET charset_name]
     *        export_options
     *      | INTO DUMPFILE 'file_name'
     *      | INTO var_name [, var_name]]
     *    [FOR {UPDATE | SHARE} [OF tbl_name [, tbl_name] ...] [NOWAIT | SKIP LOCKED] 
     *      | LOCK IN SHARE MODE]]
     */
    protected function parseString($sql) {
        $sql = trim($sql);
        
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
            $c = $sql[$p];              // current character
            
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
    
    
    /**
     * splitQueries() - split query(ies) in an array per command
     */
    protected function splitQueries() {
        
        $state = array();
        $state['pos'] = 0;
        
        $partnos = count($this->parts);
        
        $qcpos = -1;
        
        $this->splitQueries = array();
        $this->splitQueries[] = array();
        $splitquery_no = 0;
        
        for($x=0; $x < $partnos; $x++) {
            $insertParts = 1;
            
            if ($qcpos == -1) {
                $search_startpos = 0;
            } else if (isset($this->queryCommands[$qcpos]['multi']) && $this->queryCommands[$qcpos]['multi']) {
                $search_startpos = $qcpos;
            } else {
                $search_startpos = $qcpos+1;
            }
            for($y=$search_startpos; $y < count($this->queryCommands); $y++) {
                // check if current position is start next command
                $cmds = $this->queryCommands[$y]['cmd'];
                if (is_string($cmds)) $cmds = array($cmds);
                
                $found = false;
                $lastcmd_string = null;
                foreach($cmds as $cmd) {
                    $lastcmd_string = $cmd;
                    $cmdstring = explode(' ', $cmd);
                    
                    $matchCount = 0;
                    for($z=0; $z < count($cmdstring); $z++) {
                        if ($x+($z*2) >= $partnos)
                            break;
                        
                        if (strtolower($this->parts[$x+($z*2)]['string']) == strtolower($cmdstring[$z])) {
                            $matchCount++;
                        }
                    }
                    
                    if ($matchCount == count($cmdstring)) {
                        $found = true;
                        break;
                    }
                }
                
                if ($found) {
                    
                    if ($lastcmd_string == 'union') {
                        $this->splitQueries[] = array();
                        $splitquery_no++;
                        $insertParts = 0;
                        $qcpos = -1;
                        
                        // skip next whitespace
                        $x++;
                        
                        break;
                    } else {
                        $qcpos = $y;
                        
                        $this->splitQueries[$splitquery_no][] = array(
                            'cmd' => $lastcmd_string,
                            'parts' => array()
                        );
                        
                        $insertParts = $matchCount * 2;
                    }
                    
                    break;
                }
            }
            
            for($y=0; $y < $insertParts; $y++) {
                if ($y > 0) $x++;
                
                $lastcmd = count($this->splitQueries[$splitquery_no])-1;
                $this->splitQueries[$splitquery_no][$lastcmd]['parts'][] = $this->parts[$x];
            }
        }
    }
    
    
    
    
    
}
