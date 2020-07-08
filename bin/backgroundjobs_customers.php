#!/usr/bin/env php
<?php

/**
 * backgroundjobs_customers.php - starts backgroundjobs_context.php for all customers in system
 *
 */

if (count($argv) != 1) {
    print "Usage: {$argv[0]}\n";
    exit;
}

// bootstrap
chdir(__DIR__.'/..');
include 'config/config.php';

//bootstrapCli('admin');

$lastCronRun = null;


$startedCustomers = array();
while ( true ) {
    try {
        
        // check child processes PID's
        $startedContextNames = array_keys($startedCustomers);
        foreach($startedContextNames as $contextName) {
            $settings = $startedCustomers[$contextName];
            
            $status=null;
            if (pcntl_wait( $settings['pid'], $status, WNOHANG ) === 0) {
                print_info("$contextName: Seems stopped running...");
                unset( $startedCustomers[$contextName] );
            }
        }
        
        
        // lookup current active customers
        $acs = object_container_get(\admin\service\AdminCustomerService::class);
        
        $currentCustomers = array();
        $customers = $acs->readCustomers();
        foreach($customers as $c) {
            if ($c->getActive() == false) continue;
            
            $cn = $c->getContextName();
            $currentCustomers[$cn] = true;
            
            
            // check if proces is already running
            if (isset($startedCustomers[$cn])) {
                $pid = $startedCustomers[$cn]['pid'];
                
                $status = null;
                $r = pcntl_waitpid($pid, $status, WNOHANG);
                
                if ($r === 0) {
                    // print $bj->getCmd().": Proces already running..\n";
                    continue;
                }
            }
            
            // start process
            $pid = pcntl_fork();
            if (!$pid) {
                print_info('Starting backgroundjobs_customer.php for: ' . $cn);
                $cmd = realpath(__DIR__.'/backgroundjobs_context.php');
                $args = array( $cn );
                pcntl_exec( $cmd , $args );
                die('THIS CANT BE REACHED!');
            }
            
            
            $startedCustomers[$cn] = array(
                'pid' => $pid
            );
        }
        
        // stop removed customers
        foreach($startedCustomers as $contextName => $settings) {
            // proces running?
            if (isset($currentCustomers[$contextName])) {
                // print "Process not ended? => skip\n";
                continue;
            }

            // check pid
            $status = null;
            $pid = $settings['pid'];
            $r = pcntl_waitpid($pid, $status, WNOHANG);
            
            print_info("$contextName: Stopping proces..");
            
            if ($r === 0) {
                posix_kill( $pid, 7 );
            } else {
                $proc_status = false;
            }

            $r = pcntl_waitpid($pid, $status, WNOHANG);
            
            if ($r !== 0) {
                print_info("$contextName: Stopped..");
                unset( $startedCustomers[$contextName] );
            }
            else {
                print_info("$contextName: Process still running, kill takes some time..");
            }
        }
        
        
    } catch (\Exception $ex) {
        // Exception.. close all database connections to reset state
        print_info("Error: " . $ex->getMessage());
        \core\db\DatabaseHandler::getInstance()->closeAll();
    }
    
    sleep(60);
}

