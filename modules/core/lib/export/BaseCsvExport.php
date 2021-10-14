<?php


namespace core\export;



class BaseCsvExport extends BaseExport {
    
    

    
    
    public function sendHeaders( $filename ) {
        
        if (ctx()->getVar('list-response-excel-disable-headers')) {
            
        } else if (is_web()) {
            if (endsiWith($filename, '.csv') == false) {
                $filename = $filename . '.csv';
            }
            
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
        }
        
    }
    
}


