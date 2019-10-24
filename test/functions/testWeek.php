<?php



include dirname(__FILE__).'/../bootstrap.php';


function test_week_diff($start=null, $end=null) {
    if ($start === null || $end === null) {
        $start = date('Y');
        $end = date('n');
    }
    
    $weeks_per_year = array();
    
    for($x=2000; $x < 2020; $x++) {
        $weeklist = array();
        $weeklist['weeks'] = week_list( $x );
        $weeklist['year'] = $x;
        
        $weeks_per_year[] = $weeklist;
    }
    
    $prev = null;
    foreach($weeks_per_year as $weeks_year) {
        $year = $weeks_year['year'];
        $weeks = $weeks_year['weeks'];
        
        foreach($weeks as $w) {
            $diff = week_diff($start, $end, $year, $w['weekno']);
            
            if ($prev !== null) {
                if ($diff - $prev != 1) {
                    print "ERROR: invalid value, Start: {$start}-{$end}, End: {$year}-{$w['weekno']}\n";
                }
            }
            
            $prev = $diff;
        }
    }
}

function test_show_weeknos_per_year() {
    $years_with_weeks_53 = array( 1903, 1908, 1914, 1920, 1925, 1931, 1936, 1942, 1948, 1953, 1959, 1964, 1970, 1976, 1981, 1987, 1992, 1998, 2004, 2009, 2015, 2020, 2026, 2032, 2037, 2043, 2048, 2054, 2060, 2065, 2071, 2076, 2082, 2088, 2093, 2099 );
    
    $start = 1900;
    $end = 2100;
    
    for($x=$start; $x < $end; $x++) {
        $wl = week_list($x);
        
        $weekCount = count($wl);
        $lastweek = $wl[$weekCount-1];
        
//         print "Start $x: " . $wl[0]['year'].'-'.$wl[0]['weekno']."\t".$lastweek['year'].'-'.$lastweek['weekno']."\n";
        
        if ($x > 1900 && $x < 2100) {
            if ($lastweek['weekno'] == 52) {
                if (in_array($x, $years_with_weeks_53)) {
                    print "ERROR: Wrong number of weeks calculated for year $x\n";
                }
            }
            else if ($lastweek['weekno'] == 53) {
                if (in_array($x, $years_with_weeks_53) == false) {
                    print "ERROR: Wrong number of weeks calculated for year $x\n";
                }
            }
        }
        
        if ($lastweek['weekno'] != 52 && $lastweek['weekno'] != 53){
            print "ERROR: Wrong number of weeks calculated for year $x\n";
        }
        
    }
}


function test_weeks_in_year() {
    $years_with_weeks_53 = array( 1903, 1908, 1914, 1920, 1925, 1931, 1936, 1942, 1948, 1953, 1959, 1964, 1970, 1976, 1981, 1987, 1992, 1998, 2004, 2009, 2015, 2020, 2026, 2032, 2037, 2043, 2048, 2054, 2060, 2065, 2071, 2076, 2082, 2088, 2093, 2099 );
    
    for($x=1900; $x < 2100; $x++) {
        $weeks = weeks_in_year($x);
        
        if (in_array($x, $years_with_weeks_53) && $weeks != 53) {
            print "ERROR: Weeks not right for year $x: $weeks\n";
        }
        if (in_array($x, $years_with_weeks_53) == false && $weeks != 52) {
            print "ERROR: Weeks not right for year $x: $weeks\n";
        }
    }
}

print "test_show_weeknos_per_year()\n";
test_show_weeknos_per_year();

print "test_week_diff()\n";
// test_week_diff();
// for($x=2010; $x < 2020; $x++) {
//     for($y=1; $y <= weeks_in_year($x); $y++) {
//         test_week_diff($x, $y);
//     }
// }

print "test_weeks_in_year()\n";
test_weeks_in_year();






