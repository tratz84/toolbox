<?php


namespace base\service;

use core\service\ServiceBase;
use core\container\CronContainer;
use core\ObjectContainer;
use base\model\CronDAO;
use base\model\Cron;
use core\exception\DatabaseException;
use base\model\CronRun;
use core\db\DatabaseHandler;
use base\model\CronRunDAO;
use core\exception\InvalidStateException;

/**
 *  note: ON PURPOSE this class doesn't extend ServiceBase ! Handle transactions manually, because 
 *        database-connection is closed & opened for every cron-job
 */
class CronService {
    
    
    public function runCron() {
        $cc = ObjectContainer::getInstance()->create(CronContainer::class);
        $cc->init();
        
        $cronjobs = $cc->getCronjobs();
        
        foreach($cronjobs as $c) {
            $dbcron = $this->registerCronjob( $c );
            
            $timeLastRunning = 0;
            if ( $dbcron->getLastRun() ) {
                $timeLastRunning = date2unix($dbcron->getLastRun());
            }
            
            // already running? => skip if it's less then 1 hour
            if ($dbcron->getRunning()) {
                if ((time() - $timeLastRunning) < 60 * 60)
                    continue;
            }
            
            if ($c->isDaily()) {
                // daily cron, not yet run today & after 05:00 ? => run cronjob
                if ((!$timeLastRunning || date('Y-m-d') != date('Y-m-d', $timeLastRunning)) && date('G') > 5) {
                    $dbcron->setRunning(true);
                    $dbcron->setLastStatus('started');
                    $dbcron->setLastRun(date('Y-m-d H:i:s'));
                    $dbcron->save();
                    
                    // disconnect, start cron with fresh connection
                    DatabaseHandler::getInstance()->closeAll();
                    
                    $c->run();
                    
                    // disconnect, connection might be 'dirty'
                    DatabaseHandler::getInstance()->closeAll();
                    DatabaseHandler::getConnection('default');
                    
                    $cr = new CronRun();
                    $cr->setCronId($dbcron->getCronId());
                    $cr->setMessage($c->getMessage());
                    $cr->setError($c->getError());
                    $cr->setStatus($c->getStatus());
                    $cr->save();
                    
                    $dbcron->setRunning(false);
                    $dbcron->setLastStatus( $c->getStatus() );
                    $dbcron->save();
//                     DatabaseHandler::getInstance()->commitTransaction();
//                     print DatabaseHandler::getInstance()->getLastQuery();
                    
                }
            } else {
                $runCron = false;
                if ($c->getTimeout() !== null && time()-$timeLastRunning > $c->getTimeout()) {
                    $runCron = true;
                }
                else if ($c->checkJob()) {
                    $runCron = true;
                }
                
                // daily cron, not yet run today & after 05:00 ? => run cronjob
                if ( $runCron ) {
                    // disconnect, start cron with fresh connection
                    DatabaseHandler::getInstance()->closeAll();
                    
                    $dbcron->setRunning(true);
                    $dbcron->setLastStatus('started');
                    $dbcron->setLastRun(date('Y-m-d H:i:s'));
                    $dbcron->save();
                    
                    
                    $con = DatabaseHandler::getInstance()->getConnection('default');
                    
                    // start transaction again
                    $con->beginTransaction();
                    
                    $c->run();
                    
                    $con->commitTransaction();
                    
                    $cr = new CronRun();
                    $cr->setCronId($dbcron->getCronId());
                    $cr->setMessage($c->getMessage());
                    $cr->setError($c->getError());
                    $cr->setStatus($c->getStatus());
                    $cr->save();
                    
                    $dbcron->setRunning(false);
                    $dbcron->setLastStatus('done');
                    $dbcron->save();
                }
            }
            
            
        }
    }

    protected function registerCronjob($c) {
        $cDao = new CronDAO();
        
        $cron = $cDao->readByName(get_class($c));
        if (!$cron) {
            $cron = new Cron();
            $cron->setCronName(get_class($c));
//             $cron->setCronTitle($c->getTitle());
            $cron->setLastStatus('init');
            
            if (!$cron->save())
                throw new DatabaseException('Unable to register cronjob');
        }
        
        return $cron;
    }
    
    public function readCron($cronId) {
        $cDao = new CronDAO();
        
        return $cDao->read($cronId);
    }
    
    public function readCrons() {
        $cc = ObjectContainer::getInstance()->create(CronContainer::class);
        $cc->init();
        
        $cDao = new CronDAO();
        
        $cronjobsDb = $cDao->readAll();
        
        $cronjobs = $cc->getCronjobs();
        
        // mja.. cron_title veld uit db halen?
        foreach($cronjobsDb as &$c) {
            foreach($cronjobs as $c2) {
                if (get_class($c2) == $c->getCronName()) {
                    $c->setTitle( $c2->getTitle() );
                    break;
                }
            }
        }
        
        return $cronjobsDb;
    }
    
    
    public function readCronRuns($cronId, $limit) {
        if ($limit > 1000) {
            throw new InvalidStateException('Limit too large');
        }
        
        $crDao = new CronRunDAO();
        return $crDao->readLast($cronId, $limit);
    }
    
    
}


