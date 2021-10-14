<?php


namespace core\export;

use core\db\DBObject;


class CursorCsvExport extends BaseCsvExport {
    
    
    public function export( \core\db\Cursor $cursor, $filename) {
        
        $this->sendHeaders( $filename );
        
        // write header
        $headers = $this->getHeaders();
        $out = fopen('php://output', 'w');
        $this->writeRow( $out, $headers );

        
        
        // write rows
        while ($cursor->hasNext()) {
            $obj = $cursor->next();
            
            $row = array();
            for($colno=0; $colno < count($this->fields); $colno++) {
                $f = $this->fields[$colno];
                
                if (is_a( $obj, DBObject::class )) {
                    $val = $obj->getterOrField( $f['name'] );
                }
                
                
                // callback?
                $val = $this->callCallbackColumnValue( $f['name'], $val, [ 'row' => $obj ]);
                
                $row[] = $val;
            }
            
            $this->writeRow( $out, $row );
        }
        
        fclose($out);
    }
    
    
    protected function writeRow( $stream, $arr ) {
        
        fputcsv( $stream, $arr );
        
    }
    
}


