<?php



function which_exec($file) {
    $path = getenv('PATH');
    $path .= PATH_SEPARATOR . '.'.DIRECTORY_SEPARATOR;
    
    $paths = explode(PATH_SEPARATOR, $path);
    
    foreach($paths as $p) {
        $p = trim($p);
        if ($p == '')
            continue;
        
        $f = realpath($p).'/'.$file;
        $f = realpath($f);
        
        if ($f) {
            return $f;
        }
    }
    
    return null;
}


function exec_return_stdout($cmd, $stdin, $cwd=null, $env=null, $other_options=null) {
    list($return_value, $stdout) = exec_return($cmd, $stdin, $cwd, $env, $other_options);
    
    return $stdout;
}

function exec_return($cmd, $stdin, $cwd=null, $env=null, $other_options=null) {
    $pipes = array();
    $ds = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//         2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
    );
    
    $stdout = null;
    $p = proc_open($cmd, $ds, $pipes, $cwd, $env, $other_options);
    if (is_resource($p)) {
        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);
        
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
    }
    
    $return_value = proc_close($p);
    
    return [$return_value, $stdout];
}


