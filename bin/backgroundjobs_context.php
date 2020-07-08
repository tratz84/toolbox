#!/usr/bin/env php
<?php

use base\service\SettingsService;

/**
 * background_context.php - handles background processes & cronjobs for given context
 *
 */

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// bootstrap
chdir(__DIR__.'/..');
include 'config/config.php';

$contextName = $argv[1];
bootstrapCli($contextName);

$lastCronRun = null;

$settingsService = object_container_get( SettingsService::class );
$modules_sig = $settingsService->enabledModulesSignature();



$startedProcesses = array();
while ( true ) {
    try {
        
        // check if enabled modules has changes. Yes? => restart script (just end, backgroundjobs_customers.php will pick it up..)
        if ($modules_sig != $settingsService->enabledModulesSignature()) {
            print_info("Exiting... loaded modules have been changed");
            exit;
        }
        
        // publish event for starting any crons
        $ac = new \core\container\ArrayContainer();
        hook_eventbus_publish($ac, 'core', 'background-jobs');
        
        print_info("Lookup background-jobs.. ".$ac->count());

        // check which crons has to be started
        $currentProcesses = array();
        foreach($ac->getItems() as $bj) {
            // mark as running
            $currentProcesses[ $bj->getCmd() ] = true;
            
            // check if proces is already running
            if (isset($startedProcesses[$bj->getCmd()])) {
                $pid = $startedProcesses[$bj->getCmd()]['pid'];
                
                $status = null;
                $r = pcntl_waitpid($pid, $status, WNOHANG);
                
                if ($r === 0) {
                    // print $bj->getCmd().": Proces already running..\n";
                    continue;
                }
            }
            
            // determine path to command
            $cmd = $bj->getCmd();
            $params = '';
            if (strpos($cmd, ' ') !== false) {
                $params = substr($cmd, strpos($cmd, ' ')+1);
                $cmd = substr($cmd, 0, strpos($cmd, ' '));
            }
            if (strpos($cmd, 'modules/') === 0) {
                $module = substr($cmd, 8, strpos($cmd, '/', 8)-8);
                $path = substr($cmd, strpos($cmd, '/', 8));
                
                $cmd = module_file($module, $path);
            }
            else {
                $cmd = realpath($cmd);
            }
            
            // check if command is found
            if ($cmd == false) {
                print_info("Error: Unable to execute " . $bj->getCmd());
                continue;
            }
            
            // .php-file? => start with 'php -f'..
            if (endsWith($cmd, '.php')) {
                // $cmd = 'php -f ' . $cmd;
            }
            
            // start proces
            print_info("$cmd: Starting proces..");
            $descriptorspec = array(
               // 0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
               // 1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
               // 2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
            );
            $pipes = array();
            
            $pid = pcntl_fork();
            if (!$pid) {
                $args = explode(' ', $params);
                pcntl_exec( $cmd , $args );
                die('THIS CANT BE REACHED!');
            }
            
            $startedProcesses[$bj->getCmd()] = array(
                'pid' => $pid
            );
        }
        
        foreach($startedProcesses as $cmd => $settings) {
            
            // proces running?
            if (isset($currentProcesses[$cmd])) {
                // print "Process not ended? => skip\n";
                continue;
            }

            // check pid
            $status = null;
            $pid = $startedProcesses[$cmd]['pid'];
            $r = pcntl_waitpid($pid, $status, WNOHANG);
            
            print_info("$cmd: Stopping proces..");
            
            if ($r === 0) {
                posix_kill( $pid, 7 );
            } else {
                $proc_status = false;
            }

            $r = pcntl_waitpid($pid, $status, WNOHANG);
            
            if ($r !== 0) {
                print_info("$cmd: Stopped..");
                unset( $startedProcesses[$cmd] );
            }
            else {
                print_info("$cmd: Process still running, kill takes some time..");
            }
        }

        
        // run cron every 5 minuts
        if ($lastCronRun == null || $lastCronRun+(60*5) < time()) {
            print_info("CronService::runCron()");
            $cronService = object_container_get(\base\service\CronService::class);
            $cronService->runCron();
            
            $lastCronRun = time();
        }
    } catch (\Exception $ex) {
        // Exception.. close all database connections to reset state
        print_info("Error: " . $ex->getMessage());
        \core\db\DatabaseHandler::getInstance()->closeAll();
    }
    
    sleep(30);
}
