<?php

namespace project\service;

use base\model\UserDAO;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use project\form\ProjectForm;
use project\form\ProjectHourForm;
use project\form\ProjectHourStatusForm;
use project\form\ProjectHourTypeForm;
use project\model\Project;
use project\model\ProjectDAO;
use project\model\ProjectHour;
use project\model\ProjectHourDAO;
use project\model\ProjectHourStatus;
use project\model\ProjectHourStatusDAO;
use project\model\ProjectHourType;
use project\model\ProjectHourTypeDAO;



class ProjectService extends ServiceBase {


    public function searchProject($start, $limit, $opts = array()) {
        $pDao = new ProjectDAO();

        $cursor = $pDao->search($opts);

        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('project_id', 'project_name', 'total_minutes', 'person_id', 'firstname', 'insert_lastname', 'lastname', 'company_id', 'company_name', 'active'));

        return $r;
    }


    public function readProject($projectId) {
        $pDao = new ProjectDAO();
        $p = $pDao->read($projectId);

        return $p;
    }

    public function deleteProject($projectId) {
        $pDao = new ProjectDAO();
        $p = $pDao->read($projectId);

        if ($p == null) {
            throw new ObjectNotFoundException('Project not found');
        }

        $phDao = new ProjectHourDAO();
        $phDao->deleteByProject($projectId);

        $pDao->delete($projectId);
    }


    public function saveProject(ProjectForm $form) {
        $id = $form->getWidgetValue('project_id');
        if ($id) {
            $project = $this->readProject($id);
        } else {
            $project = new Project();
        }


        $form->fill($project, array('project_id', 'project_name', 'active', 'note'));

        $project->setCompanyId(null);
        $project->setPersonId(null);

        $customer_id = $form->getWidgetValue('customer_id');
        if (strpos($customer_id, 'company-') === 0)
            $project->setCompanyId(str_replace('company-', '', $customer_id));
        if (strpos($customer_id, 'person-') === 0)
            $project->setPersonId(str_replace('person-', '', $customer_id));


        if (!$project->save()) {
            return false;
        }
    }

    public function readByCustomer($companyId=null, $personId=null) {
        $pDao = new ProjectDAO();

        if ($companyId) {
            return $pDao->readByCompany($companyId);
        }

        if ($personId) {
            return $pDao->readByPerson($personId);
        }

        return array();
    }



    public function searchHour($start, $limit, $opts = array()) {
        $pDao = new ProjectHourDAO();

        $cursor = $pDao->search($opts);

        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('project_hour_id', 'project_id', 'project_name', 'short_description', 'start_time', 'end_time', 'duration', 'total_minutes', 'declarable', 'user_id', 'username', 'company_id', 'company_name', 'person_id', 'firstname', 'insert_lastname', 'lastname', 'status_description', 'created'));

        return $r;
    }

    public function readHour($projectHourId) {
        $phDao = new ProjectHourDAO();

        $ph = $phDao->read($projectHourId);

        return $ph;
    }

    public function deleteHour($hourId) {
        $phDao = new ProjectHourDAO();

        $phDao->delete($hourId);
    }


    public function saveProjectHour(ProjectHourForm $form) {
        $id = $form->getWidgetValue('project_hour_id');

        if ($id) {
            $hour = $this->readHour($id);
        } else {
            $hour = new ProjectHour();
        }

        $form->fill($hour, array('project_hour_id', 'project_id', 'user_id', 'declarable', 'project_hour_type_id', 'project_hour_status_id', 'registration_type', 'start', 'end', 'duration', 'short_description', 'long_description'));

        $start = format_datetime($form->getWidgetValue('start_time'), 'Y-m-d H:i:s');
        $end = format_datetime($form->getWidgetValue('end_time'), 'Y-m-d H:i:s');
        $hour->setStartTime($start);

        if ($hour->getRegistrationType() == 'from_to') {
            $hour->setEndTime($end);
            $hour->setDuration(null);
        }
        if ($hour->getRegistrationType() == 'duration') {
            $hour->setEndTime(null);
        }

        if (!$hour->save()) {
            return false;
        }
    }



    public function readHourTypes() {
        $phtDao = new ProjectHourTypeDAO();
        return $phtDao->readAll();
    }

    public function readHourType($id) {
        $phtDao = new ProjectHourTypeDAO();
        return $phtDao->read($id);
    }
    public function deleteHourType($id) {
        $phDao = new ProjectHourDAO();
        $phDao->typeToNull($id);

        $phtDao = new ProjectHourTypeDAO();
        return $phtDao->delete($id);
    }

    public function saveHourType(ProjectHourTypeForm $form) {
        $id = $form->getWidgetValue('project_hour_type_id');
        if ($id) {
            $hourType = $this->readHourType($id);
        } else {
            $hourType = new ProjectHourType();
        }

        $form->fill($hourType, array('project_hour_type_id', 'description', 'visible', 'default_selected'));

        if (!$hourType->save()) {
            return false;
        }


        if ($hourType->getDefaultSelected()) {
            $phDao = new ProjectHourTypeDAO();
            $phDao->unsetDefaultSelected($hourType->getProjectHourTypeId());
        }
    }




    public function readHourStatuses() {
        $phsDao = new ProjectHourStatusDAO();
        return $phsDao->readAll();
    }

    public function readHourStatus($id) {
        $phsDao = new ProjectHourStatusDAO();
        return $phsDao->read($id);
    }
    public function deleteHourStatus($id) {
        $phDao = new ProjectHourDAO();
        $phDao->statusToNull($id);

        $phsDao = new ProjectHourStatusDAO();
        return $phsDao->delete($id);
    }

    public function updateHourStatusSort($ids) {
        $phsDao = new ProjectHourStatusDAO();

        $phsDao->updateSort($ids);
    }
    
    
    public function updateHourTypeSort($ids) {
        $phtDao = new ProjectHourTypeDAO();
        
        $phtDao->updateSort($ids);
    }


    public function updateHourStatus($projectHourStatusId, $projectHourIds) {
        $phDao = new ProjectHourDAO();

        foreach($projectHourIds as $id) {
            if (intval($id)) {
                $phDao->setStatusId($id, $projectHourStatusId);
            }
        }
    }



    public function saveHourStatus(ProjectHourStatusForm $form) {
        $id = $form->getWidgetValue('project_hour_status_id');
        if ($id) {
            $hourStatus = $this->readHourStatus($id);
        } else {
            $hourStatus = new ProjectHourStatus();
        }

        $form->fill($hourStatus, array('project_hour_status_id', 'description', 'default_selected'));

        if (!$hourStatus->save()) {
            return false;
        }

        if ($hourStatus->getDefaultSelected()) {
            $phDao = new ProjectHourStatusDAO();
            $phDao->unsetDefaultSelected($hourStatus->getProjectHourStatusId());
        }

    }
    
    
    public function totalsPerMonth($startPeriod, $endPeriod) {
        
        if (preg_match('/^\\d{4}-\\d{2}$/', $startPeriod) == false) {
            throw new InvalidStateException('Startperiod not valid');
        }
        if (preg_match('/^\\d{4}-\\d{2}$/', $endPeriod) == false) {
            throw new InvalidStateException('Endperiod not valid');
        }
        
        $pDao = new ProjectDAO();
        
        $totals = $pDao->totalsPerMonth($startPeriod, $endPeriod);
        
        $list = array();
        $start = format_date($startPeriod.'-15', 'Y-m-15');
        $end = format_date($endPeriod.'-15', 'Y-m-15');
        
        $ymStart = (int)format_date($start, 'Ym');
        $ymEnd = (int)format_date($end, 'Ym');
        while($ymStart <= $ymEnd) {
            $month = format_date($start, 'Y-m');
            $count = 0;
            
            foreach($totals as $t) {
                if ($t['month'] == $month) {
                    $count = $t['hours'];
                    break;
                }
            }
            
            $list[] = array(
                'month' => $month,
                'amount' => $count,
                'hours' => $count
            );
            
            $start = next_month($start);
            $ymStart = (int)format_date($start, 'Ym');
        }
        
        return $list;
    }
    
    
    
    
    public function mapProjectUsers() {
        $sql = "select distinct project__project_hour.user_id, base__user.username
                from project__project_hour
                left join base__user on (base__user.user_id = project__project_hour.user_id)
                order by username";
        
        $uDao = new UserDAO();
        $users = $uDao->queryList($sql);
        
        $r = array();
        foreach($users as $u) {
            if ($u->getUsername()) {
                $r[$u->getUserId()] = $u->getUsername();
            } else {
                // user might be deleted
                $r[$u->getUserId()] = $u->getUserId();
            }
        }
        
        return $r;
    }
    
    
    /**
     * returns start-time of first registered time
     */
    public function readFirstProjectStartTime() {
        $phDao = new ProjectHourDAO();
        
        return $phDao->readFirstStartTime();
    }
    
    public function readSummaryForMonth($userId, $year, $month) {
        $year = (int)$year;
        $month = (int)$month;
        
        $phDao = new ProjectHourDAO();
        $summary = $phDao->userSummaryForMonth($userId, $year, $month);
        
        $t = mktime(0, 0, 0, $month, 15, $year);
        $daysInMonth = date('t', $t);
        
        $result = array();
        for($day=1; $day <= $daysInMonth; $day++) {
            
            if (isset($summary[$day])) {
                $result[$day] = $summary[$day];
            } else {
                $result[$day] = 0;
            }
        }
        
        return $result;
    }
    

}
