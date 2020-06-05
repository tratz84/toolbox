<?php



use core\Context;
use core\exception\FileException;
use core\exception\NotForLiveException;
use core\exception\InvalidStateException;

function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_cli() {
    return php_sapi_name() == 'cli';
}

function is_web() {
    return php_sapi_name() != 'cli';
}

function is_windows() {
    if (strpos(PHP_OS, 'WIN') === 0) {
        return true;
    }
    
    return false;
}

function is_debug() {
    if (defined('DEBUG') && DEBUG) {
        return true;
    } else {
        return false;
    }
}

/** @return \core\Context */
function ctx() {
    return \core\Context::getInstance();
}


function is_admin_context() {
    if (defined('ADMIN_CONTEXT') && ADMIN_CONTEXT == true) {
        return true;
    } else {
        return false;
    }
}


function appUrl($u) {
    
    if (defined('ADMIN_CONTEXT')) {
        $contextName = 'admin';
    } else {
        $contextName = Context::getInstance()->getContextName();
    }
    
    if (strpos($u, '/'.$contextName.'/') === 0) {
        return $u;
    }
    
    if (is_standalone_installation()) {
        $url = BASE_HREF . substr($u, 1);
    } else {
        $url = BASE_HREF . $contextName . $u;
    }
    
    $url = apply_filter('appUrl', $url);
    
    return $url;
}

/**
 * app_request_uri() - returns (relative) request_uri. Filters BASE_HREF & user-contextName
 */
function app_request_uri() {
    if (is_standalone_installation()) {
        return '/'.substr($_SERVER['REQUEST_URI'], strlen(BASE_HREF));
    } else {
        $uri = '/'.substr($_SERVER['REQUEST_URI'], strlen(BASE_HREF));
        
        // '/module/' paths doesn't contain contextNames
        if (strpos($uri, '/module/') === 0)
            return $uri;
        
        $matches = array();
        if (preg_match('/^\\/([a-zA-Z0-9_-]+)?\\/.*/', $uri, $matches) == false) {
            throw new \core\exception\ContextNotFoundException('context not found');
        }
        
        $uri = substr($uri, strlen($matches[1])+1);
        return $uri;
    }
}

function request_uri_no_params() {
    $uri = $_SERVER['REQUEST_URI'];
    $p = strrpos($uri, '?');
    
    if ($p !== false) {
        $uri = substr($uri, 0, $p);
    }
    
    return $uri;
}


function redirect($url) {
    
    // TODO: check if $url is prefixed? & clean up?
    
    header('Location: ' . appUrl($url));
    exit;
}

function remote_addr() {
    
    // wrapper for support of proxy's in the future
    
    return $_SERVER['REMOTE_ADDR'];
}


function list_files($path, $opts=array()) {
    $path = realpath( $path );
    
    if (!$path)
        return false;
    
    if (isset($opts['basepath']) == false)
        $opts['basepath'] = $path;
    
    $dh = opendir( $path );
    if (!$dh) return false;
    
    $files = array();
    
    while ($f = readdir($dh)) {
        if ($f == '.' || $f == '..') continue;
        
        if (isset($opts['dironly']) && $opts['dironly'] && is_dir($path.'/'.$f) == false) {
            continue;
        }

        $skipFile = false;
        if (isset($opts['fileonly']) && $opts['fileonly'] && is_dir($path.'/'.$f) == true) {
            $skipFile = true;
        }
        
        if ($skipFile == false) {
            if (isset($opts['append-slash']) && $opts['append-slash'] && is_dir( $path . '/' . $f )) {
                $files[] = $f . '/';
            } else {
                $files[] = $f;
            }
        }
        
        
        if (isset($opts['recursive']) && $opts['recursive'] && is_dir($path.'/'.$f)) {
            $subfiles = list_files($path . '/' . $f, $opts);
            
            for($x=0; $x < count($subfiles); $x++) {
                $subfile = $subfiles[$x];
                
                if (strpos($subfiles[$x], $opts['basepath']) !== false)
                    $subfile = substr($subfile, strlen($opts['basepath']));
                
                $subfile = $f . '/' . $subfiles[$x];
                
                $subfiles[$x] = $subfile;
            }
            
            $files = array_merge($files, $subfiles);
        }
        
    }
    
    return $files;
}

function file_extension($file) {
    $p = strrpos($file, '.');
    
    if ($p === false) {
        return false;
    }
    
    $ext = substr($file, $p+1);
    $ext = strtolower($ext);
    
    return $ext;
}

function file_mime_type($file) {
    
    $mime_types = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'less' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    
    $ext = file_extension($file);
    
    if (isset($mime_types[$ext])) {
        return $mime_types[$ext];
    }
    
    return 'application/octet-stream';
}



function load_php_file($file) {
    $r = include $file;
    return $r;
}


function get_var($key, $defaultVal=null) {
    $ctx = Context::getInstance();
    
    return $ctx->getVar($key, $defaultVal);
}

function has_file($paramName) {
    if (isset($_FILES[$paramName]) && isset($_FILES[$paramName]['size']) && $_FILES[$paramName]['size'] > 0) {
        return true;
    }
    
    return false;
}

function save_upload_to($paramFile, $pathInDatadir) {
    $filename = basename( $_FILES[$paramFile]['name'] );
    
    $path = $pathInDatadir . '/' . $filename;
    $path = preg_replace('/\\/+/', '/', $path);
    $path = preg_replace('/\\\\+/', '/', $path);
    
    return save_upload($paramFile, $path);
}
function save_upload($paramFile, $path) {
    $ctx = Context::getInstance();
    
    $datadirContext = realpath($ctx->getDataDir());
    if ($datadirContext == false)
        throw new FileException('DATA_DIR doesn\'t exist');
    
    if (file_exists($datadirContext) == false) {
        if (mkdir($datadirContext, 0755, true) == false) {
            throw new FileException('Unable to create DATA_DIR for ' . $ctx->getContextName());
        }
    }
    
    $datadirContext = realpath($datadirContext);

    // shouldn't/cant happen
    if ($datadirContext == false) {
        throw new FileException('Data directory not found for ' . $ctx->getContextName());
    }
    
    $fullpath = $datadirContext . '/' . $path;
    $filename = basename($path);
    
    $dir = dirname( $fullpath);
    if (file_exists($dir) == false) {
        if (!mkdir($dir, 0755, true))
            return false;
    }
    
    // check if dir is within contextName's path
    $dir = realpath($dir);
    if (strpos($dir, $datadirContext) !== 0) {
        throw new FileException('Accessing file out of DATA_DIR for context (1)');
    }
    
    $fullpath = $dir . '/' . $filename;
    if (strpos($fullpath, $datadirContext) !== 0) {
        throw new FileException('Accessing file out of DATA_DIR for context (2)');
    }
    
    $r = copy($_FILES[$paramFile]['tmp_name'], $fullpath);
    
    if ($r) {
        return substr(realpath( $fullpath ), strlen($datadirContext)+1);
    } else {
        return false;
    }
}

function save_data($file, $data) {
    $ctx = Context::getInstance();
    
    $datadirContext = realpath($ctx->getDataDir());
    if ($datadirContext == false)
        throw new FileException('DATA_DIR doesn\'t exist');
        
    if (file_exists($datadirContext) == false) {
        if (mkdir($datadirContext, 0755, true) == false) {
            throw new FileException('Unable to create DATA_DIR for ' . $ctx->getContextName());
        }
    }
    
    $datadirContext = realpath($datadirContext);
    
    // shouldn't/cant happen
    if ($datadirContext == false) {
        throw new FileException('Data directory not found for ' . $ctx->getContextName());
    }
    
    $fullpath = $datadirContext . '/' . $file;
    $filename = basename($file);
    
    $dir = dirname( $fullpath );
    if (file_exists($dir) == false) {
        if (!mkdir($dir, 0755, true))
            return false;
    }
    
    // check if dir is within contextName's path
    $dir = realpath($dir);
    if (strpos($dir, $datadirContext) !== 0) {
        throw new FileException('Accessing file out of DATA_DIR for context (1)');
    }
    
    $fullpath = $dir . '/' . $filename;
    if (strpos($fullpath, $datadirContext) !== 0) {
        throw new FileException('Accessing file out of DATA_DIR for context (2)');
    }
    
    $r = file_put_contents($fullpath, $data);
    
    if ($r !== false) {
        return substr(realpath( $fullpath ), strlen($datadirContext)+1);
    } else {
        return false;
    }
}


function delete_data_file($f) {
    $file = get_data_file($f);
    
    if ($file === false) {
        return false;
    }
    
    return unlink($file);
}

function delete_data_path($path, $recursive = false) {
    $fullpath = get_data_file($path);
    
    if ($fullpath === false)
        return false;
    
    if (is_file($fullpath)) {
        return unlink($fullpath);
    } else if (is_dir($fullpath)) {
        if ($recursive) {
            $subfiles = list_data_directory( $path );
            
            foreach($subfiles as $subfile) {
                delete_data_path( $path . '/' . $subfile, true );
            }
        }
        
        return rmdir( $fullpath );
    }
}

/**
 * lists only files (not directories)
 */
function list_data_files($pathInDataDir) {
    $ctx = Context::getInstance();
    
    $datadirContext = realpath($ctx->getDataDir());
    if ($datadirContext == false)
        return array();
        
    $dir = realpath( $datadirContext . '/' . $pathInDataDir );
    if (strpos($dir, $datadirContext) !== 0)
        return array();
    
    $dh = opendir($dir);
    $files = array();
    while ($f = readdir($dh)) {
        if (is_file($dir . '/' . $f)) {
            $files[] = $f;
        }
    }
    closedir($dh);
    
    return $files;
}

/**
 * lists all files + directories
 */
function list_data_directory($pathInDataDir) {
    $ctx = Context::getInstance();
    
    $datadirContext = realpath($ctx->getDataDir());
    if ($datadirContext == false)
        return array();
        
        $dir = realpath( $datadirContext . '/' . $pathInDataDir );
    if (strpos($dir, $datadirContext) !== 0)
        return array();
    
    $dh = opendir($dir);
    $files = array();
    while ($f = readdir($dh)) {
        if ($f == '.' || $f == '..') continue;
        
        $files[] = $f;
    }
    closedir($dh);
    
    return $files;
}


function get_data_file_safe($basePath, $subPath) {
    // get fullpath for base
    $basePath = get_data_file( $basePath );
    if (!$basePath) {
        return false;
    }
    
    $fullpath = realpath( $basePath . '/' . $subPath);
    if ($fullpath == false) {
        return false;
    }
    
    if (strpos($fullpath, $basePath) === 0) {
        return $fullpath;
    }
    
    return false;
}

function get_data_file($f) {
    $ctx = Context::getInstance();

    $datadirContext = realpath($ctx->getDataDir());
    if ($datadirContext == false)
        return false;
    
    $dir = realpath( dirname($datadirContext . '/' . $f) );
    if (strpos($dir, $datadirContext) !== 0)
        return false;
    
    $file = realpath($datadirContext . '/' . $f);
    if ($file == false)
        return false;
    
    if (strpos($file, $datadirContext) !== 0)
        return false;
    
    return $file;
}

function get_data_bytes($f) {
    $file = get_data_file($f);
    if (!$file) {
        return false;
    }
    
    return file_get_contents( $file );
}


function copy_data_tmp($file, $tmpname=null) {
    // create temp-folder
    $tmpfolder = get_data_file('/tmp');
    if ($tmpfolder == false) {
        $ctx = Context::getInstance();
        $f = $ctx->getDataDir();
        
        if (mkdir($f . '/tmp', 0755) == false) {
            throw new FileException('Unable to create temp-folder');
        }
        
        $tmpfolder = get_data_file('/tmp');
    }
    
    if ($tmpfolder === false || file_exists($tmpfolder) == false) {
        throw new FileException('Temp-folder not found');
    }
    
    
    // no name specified? => generate temp-file name
    $dest = null;
    if ($tmpname === null) {
        for($x=0; $x < 50; $x++) {
            $f = 'temp'.date('Ymd').rand(0, 999999999).'-'.basename($file);
            
            if (file_exists($tmpfolder . '/' . $f) == false) {
                $dest = $tmpfolder . '/' . $f;
                break;
            }
        }
        
        if ($dest === null) {
            throw new FileException('Unable to determine temp-filename');
        }
    }
    // tempname specified
    else {
        $dest = $tmpfolder . '/' . basename($tmpname);
    }
    
    if (copy($file, $dest)) {
        return $dest;
    } else {
        throw new FileException('Unable to copy file to temp-folder');
    }
}


function url_data_file($f) {
    if (get_data_file($f)) {
        return appUrl('/?m=core&c=file&f='.urlencode($f));
    }
    
    return '';
}


function format_filesize($bytes) {
    $bytes = (int)$bytes;
    
    if ($bytes <= 1024) {
        $t = $bytes . ' bytes';
    } else if ($bytes <= 1024 * 1024) {
        $t = myround($bytes/1024, 2) . ' kb';
    } else if ($bytes <= 1024 * 1024 * 1024) {
        $t = myround($bytes/(1024 * 1024), 2) . ' mb';
    } else {//if ($bytes <= 1024 * 1024 * 1024 * 1024) {
        $t = myround($bytes/(1024 * 1024 * 1024), 2) . ' gb';
    }
    
    $t = str_replace('.', ',', $t);
    
    return $t;
}



function array_remove_value($arr, $val) {
    $newArr = array();
    
    foreach($arr as $a) {
        if ($a == $val) continue;
        $newArr[] = $a;
    }
    
    return $newArr;
}

function pos_in_array($needle, $array) {
    foreach($array as $key => $val) {
        if ($needle == $val)
            return $key;
    }
    
    return false;
}

/**
 * check_array() - checks if $variable[..] is an array and contains min-count elements
 */
function check_array($var, $field=null, $minCount=0) {
    if (is_array($var) == false)
        return false;
    
    if ($field != null) {
        if (isset($var[$field]) == false || is_array($var[$field]) == false) {
            return false;
        }
        
        $var = $var[$field];
    }
    
    if (count($var) < $minCount)
        return false;
    
    return true;
}


function lookupModuleFile($pathInModule) {
    $ctx = Context::getInstance();
    
    $basepath = realpath(ROOT . '/modules');
    foreach($ctx->getEnabledModules() as $module) {
        
        $f = realpath($basepath . '/' . $module . '/' . $pathInModule);
        if ($f)
            return $f;
    }
    
    return false;
}




function date2unix($input)
{
    $input = trim($input);

    if (strpos($input, '/Date(') !== false) {
        $matches = array();
        
        if (preg_match('/\/Date\((\\d+)\\)\\//', $input, $matches) && count($matches) == 2) {
            return intval($matches[1] / 1000);
        }
    }

    if (preg_match('/^\\d{8}+$/', $input)) {
        $d2uy = (int)substr($input, 0, 4);
        $d2um = (int)substr($input, 4, 2);
        $d2ud = (int)substr($input, 6, 2);

        if ($d2uy > 1850 && $d2uy < 2500 && $d2um >= 1 && $d2um <= 12 && $d2ud >= 1 && $d2ud <= 31) {
            return mktime(12, 0, 0, $d2um, $d2ud, $d2uy);
        }
    }


    
    if ($input == "0000-00-00") { // ongeldige datum
        return null;
    } else if (preg_match('/^\\d{4}-\\d{1,2}-\\d{1,2} \\d{2}:\\d{2}:\\d{2}$/', $input)) { // jaar-maand-dag uur:minuut:seconde
        list ($date, $time) = explode(' ', $input);
        $d = explode('-', $date);
        $t = explode(':', $time);
    } else if (preg_match('/^\\d{1,2}-\\d{1,2}-\\d{4} \\d{2}:\\d{2}:\\d{2}$/', $input)) { // dag-maand-jaar uur:minuut:seconde
        list ($date, $time) = explode(' ', $input);

        $d = explode("-", $input);
        $d = array(
            $d[2],
            $d[1],
            $d[0]
        );

        $t = explode(':', $time);
    } else if (preg_match('/^\\d{1,2}-\\d{1,2}-\\d{4} \\d{2}:\\d{2}$/', $input)) { // dag-maand-jaar uur:minuut
        list ($date, $time) = explode(' ', $input);

        $d = explode("-", $date);
        $d = array(
            $d[2],
            $d[1],
            $d[0]
        );

        $t = explode(':', $time);
        $t[] = 0;
    } else if (preg_match("/^\\d{1,2}-\\d{1,2}-\\d{4}$/", $input)) {
        $d = explode("-", $input);
        $d = array(
            $d[2],
            $d[1],
            $d[0]
        );
    } else if (preg_match('/^\\d{4}\\/\\d{1,2}\\/\\d{1,2}$/', $input)) {
        $d = explode("/", $input);
    } else if (preg_match('/^\\d{4}-\\d{1,2}-\\d{1,2}$/', $input)) {
        $d = explode("-", $input);
    } else {
        return null;
    }

    if (isset($t)) {
        $t = explode(":", substr($input, 10));
        if (count($t) == 2) $t[] = 0;

        return mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
    }

    return mktime(0, 0, 0, $d[1], $d[2], $d[0]);
}


function format_date($str, $format='d-m-Y', $defaultVal='') {

    if (strpos($str, 'Y-m') !== false || strpos($str, 'm-Y') !== false || $str == 'Ymd') {
        throw new NotForLiveException('Lul, je draait de $str & $format weer eens om...');
    }
    
    if (valid_date($str) || valid_datetime($str)) {
        $t = date2unix($str);
        
        if ($t) {
            return date($format, $t);
        }
    }
    
    return $defaultVal;
}

function date2number($date) {
    $t = date2unix($date);
    
    return (int)date('Ymd', $t);
}


function format_datetime($str, $format='d-m-Y H:i:s', $defaultVal='') {
    
    if (valid_datetime($str)) {
        $t = date2unix($str);

        if ($t) {
            return date($format, $t);
        }
    }
    
    return $defaultVal;
}

/**
 * previous_month() - calculates previous month. If last day of month is selected, stick to it
 */
function previous_month($date, $no=1) {
    if ($no == 0)
        return $date;
    
    $year = format_date($date, 'Y');
    $month = format_date($date, 'n');
    $day = format_date($date, 'j');
    
    $daysInMonth = date('t', date2unix($date));
    
    $t = mktime(0, 0, 0, $month, 15, $year);
    $t = strtotime('-'.$no.' month', $t);
    
    if ($day == $daysInMonth || $day > date('t', $t)) {
        return date('Y-m-t', $t);
    } else {
        return sprintf('%s-%02d', date('Y-m', $t), $day);
    }
}

/**
 * next_month() - calculates next month. If last day of month is selected, stick to it
 */
function next_month($date, $no=1) {
    if ($no == 0)
        return $date;
    
    $year = format_date($date, 'Y');
    $month = format_date($date, 'n');
    $day = format_date($date, 'j');
    
    $daysInMonth = date('t', date2unix($date));
    
    $t = mktime(0, 0, 0, $month, 15, $year);
    $t = strtotime('+'.$no.' month', $t);
    
    if ($day == $daysInMonth || $day > date('t', $t)) {
        return date('Y-m-t', $t);
    } else {
        return sprintf('%s-%02d', date('Y-m', $t), $day);
    }
}

function next_day($date, $no=1) {
    if ($no == 0)
        return $date;
        
    $year = format_date($date, 'Y');
    $month = format_date($date, 'n');
    $day = format_date($date, 'j');
    
    $t = mktime(12, 0, 0, $month, $day, $year);
    $t = strtotime(($no<0?'-':'+') . abs($no) . ' days', $t);
    
    return date('Y-m-d', $t);
}

function weeks_in_year($year, $timezone='Europe/Amsterdam') {
    $dt = new DateTime($year . '-12-30', new DateTimeZone($timezone));
    
    // ISO-8601 specification, 28 dec always last week
    $dt->setDate($year, 12, 28);
    
    return $dt->format('W');
}

/**
 * week_list() - returns array with weeks, year & start date
 * 
 * week_list(2019) returns,
 *  array(
 *      array('weekno' => '01'..., 'year' => '2018', 'monday' => '2018-12-31'),
 *      ...
 *      array('weekno' => '52'..., 'year' => '2019', 'monday' => '2018-12-23'),
 *  )
 */
function week_list($year, $timezone='Europe/Amsterdam') {
    $r = array();
    
    $dt = new DateTime(($year-1).'-12-23', new DateTimeZone($timezone));
    
    while ($dt->format('W') > 50) {
        $dt->modify('+1 day');
    }
    
    $x=0;
    $blnWeek1Set = false;
    while(true) {
        // monday?
        if ($dt->format('N') == 1) {
            $weekno = $dt->format('Y-W');
            
            // another 01-week? => next year => break
            if ($dt->format('W') == '01' && $blnWeek1Set) {
                break;
            }
            
            // mark week 01 as set
            if ($dt->format('W') == '01') {
                $blnWeek1Set = true;
            }
            
            $r[] = array(
                'weekno' => (int)$dt->format('W'),
                'year' => $dt->format('Y'),
                'monday' => $dt->format('Y-m-d')
            );
        }
        
        $dt->modify('+1 day');
    }
    
    return $r;
}

/**
 * week_diff() - returnweeks between start & end
 */
function week_diff($p_startYear, $p_startWeek, $p_endYear, $p_endWeek) {

    $startYear = (int)$p_startYear;
    $startWeek = (int)$p_startWeek;
    $endYear = (int) $p_endYear;
    $endWeek = (int)$p_endWeek;
    
    if ($startYear == $endYear && $startWeek == $endWeek) {
        return 0;
    }
    
    $negative = false;
    if ($startYear > $endYear || ($startYear == $endYear && $startWeek > $endWeek)) {
        $negative = true;
    }
    
    $startDate = null;
    $weeklistStart = week_list($startYear);
    if ($startWeek < 1 || $startWeek > count($weeklistStart)) {
        throw new InvalidStateException('Invalid start week/year ('.$p_startWeek.'/'.$p_startYear.')');
    }
    $startDate = $weeklistStart[$startWeek-1]['monday'];
    
    $endDate = null;
    $weeklistEnd = week_list($endYear);
    if ($endWeek < 1 || $endWeek > count($weeklistEnd)) {
        throw new InvalidStateException('Invalid end week/year ('.$p_endWeek.'/'.$p_endYear.')');
    }
    $endDate = $weeklistEnd[$endWeek-1]['monday'];
    
    $weeknos = 0;
    if ($endYear > $startYear) {
        // add weeks for this year
        $weeknos += (weeks_in_year($startYear) - $startWeek);
        
        // add weeks for years in between
        for($x=$startYear+1; $x < $endYear; $x++) {
            $weeknos += weeks_in_year($x);
        }
        
        // add weeks for last year
        $weeknos += $endWeek;
    }
    if ($endYear < $startYear) {
        // subtract weeks this year
        $weeknos -= $startWeek;
        
        for($x=$startYear-1; $x > $endYear; $x--) {
            $weeknos -= weeks_in_year($x);
        }
        
        $weeknos -= (weeks_in_year($endYear) - $endWeek);
    }
    if ($startYear == $endYear) {
        return $endWeek - $startWeek;
    }
    
    
    return $weeknos;
}



function valid_date($str) {
    if ($str == '0000-00-00' || $str == false)
        return false;
    
    if (preg_match('/^\\d\\d-\\d\\d-\\d\\d\\d\\d$/', $str)) {
        return true;
    }
    if (preg_match('/^\\d\\d\\d\\d-\\d\\d-\\d\\d$/', $str)) {
        return true;
    }
    
    return false;
}

function valid_datetime($str) {
    
    if ($str == '0000-00-00 00:00:00')
        return false;
    
    if (preg_match('/^\\d\\d-\\d\\d-\\d\\d\\d\\d \\d\\d:\\d\\d:\\d\\d$/', $str)) {
        return true;
    }
    if (preg_match('/^\\d\\d\\d\\d-\\d\\d-\\d\\d \\d\\d:\\d\\d:\\d\\d$/', $str)) {
        return true;
    }

    if (preg_match('/^\\d\\d\\d\\d-\\d\\d-\\d\\d \\d\\d:\\d\\d$/', $str)) {
        return true;
    }
    if (preg_match('/^\\d\\d-\\d\\d-\\d\\d\\d\\d \\d\\d:\\d\\d$/', $str)) {
        return true;
    }
    
    return false;
}

function valid_time($str) {
    if (preg_match('/^\\d\\d:\\d\\d:\\d\\d$/', $str)) {
        return true;
    }
    if (preg_match('/^\\d\\d:\\d\\d$/', $str)) {
        return true;
    }
    
    return false;
}
function validate_email($email)
{
    $isValid = true;
    $atIndex = strrpos($email, "@");

    if (is_bool($atIndex) && ! $atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (! preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if (! preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless
            // local part is quoted
            if (! preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        // if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
        // {
        // // domain not found in DNS
        // $isValid = false;
        // }
    }
    return $isValid;
}


function timediff_minuts($start, $end) {
    $dt1 = new DateTime($start);
    $dt2 = new DateTime($end);
    $diff = $dt1->diff($dt2);
    
    $minuts = ($diff->days * 24 * 60) + ($diff->h*60) + $diff->i;
    
    return $minuts;
}


function slugify($str) {
    
    $str = lcfirst($str);
    $str = str_replace(['[', ']'], '-', $str);
    $str = preg_replace_callback('/[A-Z]/', function($str) { return '-'.strtolower($str[0]); }, $str);
    
    $str = strtolower($str);
    $str = trim($str);
    $str = str_replace('\\', '-', $str);
    $str = preg_replace('/[^a-z0-9 \\-\\_]/', '', $str);
    $str = str_replace(' ', '-', $str);
    $str = str_replace('_', '-', $str);
    $str = preg_replace('/\\-+/', '-', $str);
    $str = preg_replace('/^\\-+/', '', $str);
    $str = preg_replace('/\\-+$/', '', $str);
    
    return $str;
}

function limit_text($str, $maxlen, $suf='...') {
    if (strlen($str) < $maxlen) {
        return $str;
    }
    
    $str = substr($str, 0, $maxlen-(strlen($suf))) . $suf;
    
    return $str;
}

/**
 * to_php_string() - escapes a string for insertion in php-code
 * 
 * TODO: check this for security issues!! watch it before using this
 */
function to_php_string($str) {
    $str = (string)$str;
    $str = str_replace("\n", "\\n", $str);
    $str = str_replace('"', '\"', $str);
    $str = '"'.$str.'"';
    return $str;
}


function endsWith($haystack, $val) {
    $p = strrpos($haystack, $val);
    
    if ($p === false)
        return false;
    
    if ($p === (strlen($haystack) - strlen($val))) {
        return true;
    } else {
        return false;
    }
}

function endsiWith($haystack, $val) {
    $haystack = strtolower($haystack);
    $val = strtolower($val);
    
    return endsWith($haystack, $val);
}

if (function_exists('mb_trim') == false) {
    function mb_trim($str) {
        return preg_replace("/(^\s+)|(\s+$)/u", "", $str);
    }
}

function guidv4()
{
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');
        
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function valid_regexp($pattern) {
    @preg_match('/'.$pattern.'/', '');
    
    return preg_last_error() === PREG_NO_ERROR ? true : false;
}


function days_between($start, $end) {
    $s = date2unix($start);
    $e = date2unix($end);
    
    $dt1 = new DateTime(date('Y-m-d', $s));
    $dt2 = new DateTime(date('Y-m-d', $e));
    
    return $dt1->diff($dt2, true)->days;
}

function months_between($start, $end) {
    $s = date2unix($start);
    $e = date2unix($end);
    
    $dt1 = new DateTime(date('Y-m-d', $s));
    $dt2 = new DateTime(date('Y-m-d', $e));
    
    return $dt1->diff($dt2, true)->m;
}


if(!function_exists('mime_content_type')) {
    function mime_content_type($filename) {
        $mime_types = array(
            
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        
        $p = strrpos($filename, '.');
        if ($p === false) {
            return 'application/octet-stream';
        }
        
        $ext = strtolower(substr($filename, $p+1));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

/**
 * crc32_int32() - on 64-bits systems, php always uses int64's. crc32_int32 returns
 *                 the 32-bits value (in a 64-bits int...;)
 */
function crc32_int32($str) {
    $i = crc32($str);
    
    // credits to comment jian @ https://www.php.net/manual/en/function.crc32.php
    if ($i > 2147483647){
        return $i - 2147483647 * 2 - 2;
    } else {
        return $i;
    }
}




function cleanup_string($string) {
    $co = array('á', 'â', 'à', 'ä', 'é', 'ê', 'è', 'ë', 'í', 'î', 'ì', 'ï', 'ó', 'ô', 'ò', 'ö', 'ú', 'û', 'ù', 'ü', 'Á', 'Â', 'À', 'Ä', 'É', 'Ê', 'È', 'Ë', 'Í', 'Î', 'Ì', 'Ï', 'Ó', 'Ô', 'Ò', 'Ö', 'Ú', 'Û', 'Ù', 'Ü');
    $cn = array('a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U');
    
    $string = str_replace($co, $cn, $string);
    
    $str = '';
    for($x=0; $x < strlen($string); $x++) {
        $c = ord($string[$x]);
        if ($c == 9 || $c == 10 || $c == 13 || ($c >= 32 && $c <= 126)) {
            $str .= $string[$x];
        }
    }
    
    return $str;
}




