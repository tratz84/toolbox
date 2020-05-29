<?php


namespace core\db\mysql;


use core\db\DatabaseHandler;
use core\db\DBObject;
use core\exception\FileException;
use core\exception\InvalidStateException;

class MysqlTableArchiver {
    
    protected $tableName;
    protected $beforeDatetime;
    protected $outputdir = null;
    
    protected $primaryKey;
    
    public function __construct($tableName, $beforeDatetime, $outputdir=null) {
        $this->setTableName($tableName);
        $this->setBeforeDatetime($beforeDatetime);
        $this->setOutputdir($outputdir);
    }
    
    
    public function setTableName($n) { $this->tableName = $n; }
    public function getTableName() { return $this->tableName; }
    
    public function setBeforeDatetime($dt) { $this->beforeDatetime = $dt; }
    public function getBeforeDatetime() { return $this->beforeDatetime; }
    
    public function setOutputdir($d) { $this->outputdir = $d; }
    public function getOutputdir() { return $this->outputdir; }
    
    
    protected function determinePrimaryKey() {
    }
    
    
    public function execute() {
        if (is_dir( $this->getOutputdir() ) == false) {
            if (mkdir($this->getOutputdir(), 0755, true) == false) {
                throw new FileException( 'Unable to create output dir: ' . $this->getOutputdir() );
            }
        }
        
        $dbcon = DatabaseHandler::getConnection('default');
        
        // get primary key
        $pks = $dbcon->getPrimaryKey( $this->tableName );
        if (count($pks) == 0) {
            throw new InvalidStateException( "No PK found for table '".$this->tableName."'" );
        }
        
        
        // query table
        $cursor = $dbcon->queryCursor(DBObject::class, 'select * from `'.$this->tableName.'` where created < ? order by created asc', array($this->beforeDatetime));
        
        
        // archive
        $fields = null;
        
        $prevYmd = null;
        $file = null;
        $fh = null;
        
        while ($obj = $cursor->next()) {
            if ($fields === null) {
                $fields = array_keys( $obj->getFields() );
            }
            
            $ymd = format_date( $obj->getField('created'), 'Y-m-d' );
            
            if ($ymd != $prevYmd) {
                // file already open?
                if ($fh) {
                    fclose( $fh );
                    $fh = null;
                }
                
                // open/create file
                $file = $this->getOutputdir() . '/' . $this->tableName . '-' . format_date($obj->getField('created'), 'Y-m-d') . '.log';
                $fh = fopen($file, 'a');
                if (!$fh) {
                    throw new FileException( "Unable to open log-file: $file" );
                }
                
                // write header
                if (filesize( $file ) == 0) {
                    fputcsv($fh, $fields);
                }
            }
            
            // write value
            $vals = array_values( $obj->getFields() );
            fputcsv($fh, $vals);
            
            // delete record
            if (count($pks)) {
                $sql = "delete from ".$this->tableName." where ";
                $params = array();
                for($x=0; $x < count($pks); $x++) {
                    if ($x > 0)
                        $sql .= ' AND ';
                    $sql .= $pks[$x] . ' = ? ';
                    
                    $params[] = $obj->getField( $pks[$x] );
                }
                
                $dbcon->query( $sql, $params );
            }
            
            
            
            $prevYmd = $ymd;
        }
        
        // close file
        if ($fh) {
            fclose( $fh );
            $fh = null;
        }
        
        
        
    }
    
    
    
}


