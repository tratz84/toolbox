<?php

namespace codegen;


class SqlQueryParser {
    
    protected $parts;
    
    protected $queries = array();
    protected $queryNo = 0;
    
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
        array('cmd' => 'for share')
    );
    
    
    public function __construct() {
        
    }
    
    
    public function test() {
        $sql = <<<DATA
select c.company_id, `c`.`company_name` as company_name, `c` . vat_number vatnr
from customer__company c
left join customer__company_address cca on (cca.company_id = c.company_id)          # hmz
left join customer__address ca on cca.company_address_id = ca.address_id			-- blabla
where 
        c.company_id in (select company_id from customer__company where company_name like '%@%' or company_name like "% ; enzo ' \" @%")
        or c.company_name like "%'%" or c.company_name like "\"%"
        order by c.company_name
limit 10
DATA;
        
        $this->parseString($sql);
        
        $this->createQueries();
        
        print $this->toString();
        
        print 'done';
    }
    
    public function toString() {
        return $this->partsToString( );
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
    
    
    protected function addWhere($str) {
        $newParts = array();
        
        for($x=0; $x < count($this->parts); $x++) {
            $newParts[] = $this->parts[$x];
            
            if (strcasecmp($this->parts[$x]['string'], 'where') === 0) {
                $newParts[] = array(
                    'type' => 'string',
                    'string' => $str
                );
            }
        }
        
        $this->parts = $newParts;
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
    public function parseString($sql) {
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
    
    
    
    protected function createQueries() {
        
        $state = array();
        $state['pos'] = 0;
        
        
    }
    
    
    
}
