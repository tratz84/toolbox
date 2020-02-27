<?php


use core\controller\BaseController;
use core\forms\SelectField;
use project\service\ProjectService;
use base\service\UserService;

class monthlyController extends BaseController {
    
    public function init() {
        $this->addTitle(t('Month overview'));
        
    }
    
    public function action_index() {
        $this->handleProjectUsers();
        
        $this->handleMonthSelection();

        
        if ($this->selected_user_id) {
            $projectService = object_container_get(ProjectService::class);
            $this->hours = $projectService->readSummaryForMonth( $this->selected_user_id, format_date($this->selected_month, 'Y'), format_date($this->selected_month, 'm'));
            
            $userService = object_container_get(UserService::class);
            $selected_user = $userService->readUser( $this->selected_user_id );
            $this->addTitle( t('Month overview') . ' ' . $selected_user->getUsername() );
        }
        
        
        return $this->render();
    }
    
    protected function handleProjectUsers() {
        $projectService = object_container_get(ProjectService::class);
        $map = $projectService->mapProjectUsers();
        
        $this->selectUser = new SelectField('user_id', '', $map, t('User shown'));
        
        // determine user_id to show
        $user = \core\Context::getInstance()->getUser();
        if (get_var('user_id')) {
            $this->selected_user_id = (int)get_var('user_id');
        } else if (isset($map[$user->getUserId()])) {
            $this->selected_user_id = $user->getUserId();
        } else {
            $keys = array_keys($map);
            $this->selected_user_id = $keys[0];
        }
    }
    
    
    protected function handleMonthSelection() {
        $projectService = object_container_get(ProjectService::class);
        
        // determine month to start
        $firstDate = $projectService->readFirstProjectStartTime();
        // first ? => start previous month
        if ($firstDate == null) {
            $firstDate = date('Y-m-d', strtotime('-1 month'));
        } else {
            $days = days_between($firstDate, date('Y-m-d'));
            
            // date in the future? => set previous month as start
            if ($days < 0) {
                $firstDate = date('Y-m-d', strtotime('-1 month'));
            }
        }
        
        
        // build map for month selection
        $nextDate = date('Y-m-01', date2unix($firstDate));
        $ymdStop = (int)str_replace('-', '', next_month(date('Y-m-d'), 1));
        $map = array();
        do {
            $map[$nextDate] = format_date($nextDate, 'Y') . ' - ' . t('month.'.format_date($nextDate, 'm'));
            $nextDate = next_month($nextDate, 1);
            $ymdNextDate = (int)str_replace('-', '', $nextDate);
        } while ($ymdNextDate <= $ymdStop);
        
        if (get_var('month') && valid_date(get_var('month'))) {
            $this->selected_month = get_var('month');
        } else {
            $this->selected_month = date('Y-m-01');
        }
        $this->selectMonth = new SelectField('month', $this->selected_month, $map, t('Month'));
        
        // fill array of days/week
        $this->daysPerWeek = array();
        $dt = new DateTime($this->selected_month, new DateTimeZone('Europe/Amsterdam'));
        
        $daysInMonth = $dt->format('t');
        $startPos = $dt->format('N')-1;
        
        for($cnt=0; $cnt < $startPos; $cnt++) {
            $this->daysPerWeek[] = '-';
        }
        for($dayno=1; $dayno <= $daysInMonth; $dayno++) {
            $this->daysPerWeek[] = $dayno;
        }
        
        while (count($this->daysPerWeek)%7 != 0) {
            $this->daysPerWeek[] = '-';
        }
    }
    
    
}
