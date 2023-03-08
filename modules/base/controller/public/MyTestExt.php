<?php



class MyTestEx extends MyTest {
    
    
    public function __invoke ( string $name ) {
        var_export($name);
        
//         print "$name\n";
    }
    
    
}

