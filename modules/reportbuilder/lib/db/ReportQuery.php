<?php


namespace reportbuilder\db;




use core\db\DatabaseHandler;
use core\db\TableModel;
use core\db\mysql\MysqlTableGenerator;
use core\db\query\MysqlQueryBuilder;
use core\exception\InvalidStateException;
use core\forms\lists\ListResponse;
use core\forms\lists\IndexTable;
use reportbuilder\export\ExcelExport;
use core\parser\SqlQueryParser;

class ReportQuery {
    
    protected $reportName;
    protected $excelFilename = null;
    
    protected $query;                       // cache query
    protected $tableModel = null;           // internally used
    protected $hooksTableModel = array();   // hooks for updating table model on cache creation
    protected $hooksRecord = array();       // hooks for updating record on cache creation
    protected $tableCreated = false;        // cache table created? handleQuery() uses this to just once do CREATE TABLE
    
    protected $useCacheTable = false;
    
    // fields for reporting
    protected $reportFields = array();
    
    protected $form = null;
    
    /**
     * @var MysqlQueryBuilder
     */
    protected $queryBuilder = null;
    
    /**
     * @var SqlQueryParser
     */
    protected $sqlQueryParser = null;
    
    
    public function getReportName() { return $this->reportName; }
    public function setReportName($n) { $this->reportName = $n; }
    
    public function getExcelFilename() { return $this->excelFilename; }
    public function setExcelFilename($f) { $this->excelFilename = $f; }
    
    
    public function setQuery( $q ) {
        $this->query = $q;
    }
    
    
    public function addTableModelListener( $func ) {
        $this->hooksTableModel[] = $func;
    }
    
    public function addRecordListener( $func ) {
        $this->hooksRecord[] = $func;
    }
    
    public function setUseCacheTable( $bln ) { $this->useCacheTable = $bln; }
    
    
    public function setReportField($fieldName, $props=array()) {
        $this->reportFields[ $fieldName] = $props;
    }
    public function getReportFields() { return $this->reportFields; }
    
    
    public function setForm($f) { $this->form = $f; }
    public function getForm() { return $this->form; }
    
    
    /**
     * @return \core\db\query\MysqlQueryBuilder
     */
    public function &getQueryBuilder() {
        if ($this->queryBuilder) {
            return $this->queryBuilder;
        }
        
        if ($this->useCacheTable) {
            if ($this->queryBuilder == null) {
                $this->queryBuilder = DatabaseHandler::getConnection('default')->createQueryBuilder();
                $this->queryBuilder->setTable( $this->getCacheTableName() );
            }
            
            return $this->queryBuilder;
        }
        else {
            $this->queryBuilder = DatabaseHandler::getConnection('default')->createQueryBuilder();
            
            return $this->queryBuilder;
        }
    }
    
    
    /**
     * 
     * @return \core\parser\SqlQueryParser
     */
    public function &getQueryParser() {
        if ($this->sqlQueryParser) {
            return $this->sqlQueryParser;
        }
        
        if ($this->useCacheTable) {
            $this->sqlQueryParser = new SqlQueryParser();
            $this->sqlQueryParser->parseQuery("select * from ".$this->getCacheTableName());
        }
        else {
            $this->sqlQueryParser = new SqlQueryParser();
            $this->sqlQueryParser->parseQuery( $this->query );
        }
        
        return $this->sqlQueryParser;
    }
    
    
    
    public function queryList() {
        if ($this->queryBuilder) {
            $sql = $this->queryBuilder->createSelect();
            $params = $this->queryBuilder->getParams();
            
            $data = queryList('default', $sql, $params);
            
        }
        else if ( $this->sqlQueryParser ) {
            $sql = $this->sqlQueryParser->toString();
            $data = queryList('default', $sql);
        }
        else {
            throw new InvalidStateException('No QueryBuilder or SqlQueryParser set');
        }
        
        
        for($x=0; $x < count($data); $x++) {
            $this->enrichRow( $data[$x] );
        }
        
        return $data;
    }
    
    /**
     * queryToTable() - returns response in table form, with header
     */
    public function queryToTable() {
        $list = $this->queryList();
        
        $rows = array();
        
        // set header
        $row = array();
        foreach( $this->getReportFields() as $rl ) {
            $row[] = $rl['fieldDescription'];
        }
        $rows[] = $row;
        
        // set rows
        foreach($list as $l) {
            $row = array();
            
            foreach($this->getReportFields() as $key => $rl) {
                if (isset($l[$key])) {
                    $row[] = $l[$key];
                }
                else {
                    $row[] = null;
                }
            }
            
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    
    public function queryToWebDataSource() {
        $data = $this->queryToTable();
        
        $html = '';
        $html .= '<table>' . PHP_EOL;
        
        // write header
        $html .= '  <thead>' . PHP_EOL;
        $html .= '    <tr>' . PHP_EOL;
        foreach($data[0] as $val) {
            $html .= '      <td>' . esc_html($val) . '</td>';
        }
        $html .= '    </tr>' . PHP_EOL;
        $html .= '  </thead>' . PHP_EOL;
        
        
        // records
        $html .= '  <tbody>' . PHP_EOL;
        for($x=1; $x < count($data); $x++) {
            $html .= '    <tr>' . PHP_EOL;
            foreach($data[$x] as $val) {
                $html .= '      <td>' . esc_html($val) . '</td>';
            }
            $html .= '    </tr>' . PHP_EOL;
        }
        $html .= '  </tbody>' . PHP_EOL;
        
        
        $html .= '</table>' . PHP_EOL;
        
        
        return $html;
    }
    
    
    
    public function enrichRow( &$row ) { }
    
    
    public function createListResponse() {
        $data = $this->queryList();
        
        $listResponse = new ListResponse(0, count($data), count($data), $data);
        
        return $listResponse;
    }
    
    
    /**
     * @return \core\forms\lists\IndexTable
     */
    public function createIndexTable() {
        $it = new IndexTable();
        
        $it->setContainerId( '#it-'.$this->getReportName() );
        $it->setOpt('loadingIndicator', true);
        $it->setOpt('tableName', 'report-'.$this->getReportName());
        $it->enableColumnSelection();
        
        foreach($this->reportFields as $c => $props) {
            $it->setColumn($c, $props);
        }
        
        return $it;
    }
    
    /**
     * @return \reportbuilder\export\ExcelExport
     */
    public function createExcelExport() {
        $exp = new ExcelExport( $this );
        return $exp;
    }
    
    
    
    
    
    
    public function getCacheTableName() {
        return 'reportbuilder__cache_'.str_replace('-', '_', slugify( $this->reportName ));
    }
    
    
    
    
    
    
    protected function createTable( $result ) {
        // check if createTable() is already called
        if ($this->tableCreated) {
            throw new InvalidStateException('Table already created');
        }
        
        $this->tableCreated = true;
        
        $con = DatabaseHandler::getConnection('default');
        
        
        list($schemaName, $tblName) = explode('__', $this->getCacheTableName(), 2);
        
        // create new cache table
        $fields = $result->fetch_fields();
        $tb_cache = new TableModel($schemaName, $tblName);
        foreach($fields as $f) {
            $tb_cache->addColumn($f->name, $this->mysqliFieldToType( $f ));
        }

        // drop old cache table
        $con->query('drop table if exists '.$this->getCacheTableName());
        
        
        // hook for cache-table
        foreach($this->hooksTableModel as $htm) {
            $htm( $tb_cache );
        }
        
        // create table
        $mtg = new MysqlTableGenerator( $tb_cache );
        $mtg->executeDiff();
        
        return $tb_cache;
    }
    
    /**
     * handleQuery() - can be used in case cache is build by multiple queries
     */
    protected function handleQuery() {
        if ($this->tableCreated == false) {
            self::createCache();
        }
        else {
            $this->addCache();
        }
    }
    
    
    
    public function createCache() {
        if (trim($this->reportName) == '') {
            throw new InvalidStateException('Report name not set');
        }
        
        // exec query
        $con = DatabaseHandler::getConnection('default');
        $result = $con->query( $this->query );
        
        $this->tableModel = $this->createTable( $result );
        
        $this->addCache( $result );
    }
    
    public function addCache( $result = null ) {
        $con = DatabaseHandler::getConnection('default');
        
        if ($result == null) {
            // exec query
            $result = $con->query( $this->query );
        }
        
        
        $columns = $this->tableModel->getColumns();
        
        // fill cache
        $insertSql = "insert into " . $this->getCacheTableName();
        $insertSql .= " ( `" . implode('`, `', $columns) . "` )";
        $insertSql .= " VALUES ( ";
        for($x=0; $x < count($columns); $x++) {
            if ($x > 0) $insertSql .= ', ';
            $insertSql .= ' ? ';
        }
        $insertSql .= ") ";
        
        while( $row = $result->fetch_assoc() ) {
            
            // exec hook row
            foreach($this->hooksRecord as $hr) {
                $hr( $row );
            }
            
            $insertRow = array();
            foreach($columns as $c) {
                if (isset($row[$c])) {
                    $insertRow[] = $row[$c];
                }
                else {
                    $insertRow[] = null;
                }
            }
            
            $con->query($insertSql, $insertRow);
        }
        
        
    }
    
    protected function mysqliFieldToType( $field ) {
        switch ($field->type) {
            case MYSQLI_TYPE_DECIMAL:
            case MYSQLI_TYPE_NEWDECIMAL:
                // minimum 10,2
                $l = $field->max_length < 10 ? 10 : $field->max_length;
                $d = $field->decimals < 2 ? 2 : $field->decimals;
                
                return 'decimal('.$l.','.$d.')';
            case MYSQLI_TYPE_FLOAT:
            case MYSQLI_TYPE_DOUBLE:
                return 'double';
                
            case MYSQLI_TYPE_BIT:
            case MYSQLI_TYPE_TINY:
                return 'boolean';
                
            case MYSQLI_TYPE_SHORT:
            case MYSQLI_TYPE_LONG:
            case MYSQLI_TYPE_LONGLONG:
            case MYSQLI_TYPE_INT24:
            case MYSQLI_TYPE_YEAR:
            case MYSQLI_TYPE_ENUM:
                return 'int';
                
            case MYSQLI_TYPE_TIMESTAMP:
                return 'timestamp';
                
            case MYSQLI_TYPE_DATE:
                return 'date';
            case MYSQLI_TYPE_TIME:
            case MYSQLI_TYPE_DATETIME:
            case MYSQLI_TYPE_NEWDATE:
                return 'datetime';
            
            case MYSQLI_TYPE_INTERVAL:
            case MYSQLI_TYPE_SET:
            case MYSQLI_TYPE_VAR_STRING:
            case MYSQLI_TYPE_STRING:
            case MYSQLI_TYPE_CHAR:
            case MYSQLI_TYPE_GEOMETRY:
                $l = $field->max_length < 255 ? 255 : $field->max_length;
                return 'varchar('.$l.')';
            
            case MYSQLI_TYPE_TINY_BLOB:
            case MYSQLI_TYPE_MEDIUM_BLOB:
                return 'text';
            case MYSQLI_TYPE_LONG_BLOB:
            case MYSQLI_TYPE_BLOB:
                return 'longtext';
                
            default:
                throw new InvalidStateException('Unknown mysqli type');
        }
    }
    
    
}



