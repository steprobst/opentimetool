<?php
/**
 * 
 * $Id: project.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/project/treeDyn.php';
require_once $config->classPath . '/modules/project/member.php';
require_once $config->classPath . '/modules/project/tree.php';

$milestones = array(
    array('percentage' =>   0, 'color' => 'green'),
    array('percentage' => 101, 'color' => 'yellow'),
    array('percentage' => 125, 'color' => 'red'),
);

if (!$projectMember->isManager()) {
    $applError->set('You are not a project manager of any project!');
} else {
    $isAdmin = $user->isAdmin();

    // SX : Dec2012
    $filter = @$_POST['filter'];
    if (empty($filter)) {
        $filter='active';
    }

    $time->preset();
    $time->setSelect('SUM(durationSec) AS durationSumSec,projectTree_id,maxDuration');
    if (!$isAdmin) {
        $myProjects = $projectMember->getManagerProjects();
        foreach ($myProjects as $aProject) {
            $projectIds[] = $aProject['projectTree_id'];
        }
        $time->setWhere('projectTree_id IN (' . implode(',', $projectIds) . ')');
    }
    $time->addWhere(TABLE_TASK . '.calcTime=1 AND ' . TABLE_TASK . '.needsProject=1');

    switch ($config->project_overview_sort) {
        case 0:
            $time->setOrder(TABLE_PROJECTTREE . '.l');
            break;
        case 1:
            $time->setOrder(TABLE_PROJECTTREE . '.parent,' . TABLE_PROJECTTREE . '.endDate,' . TABLE_PROJECTTREE . '.name');
            break;
        case 2:
            $time->setOrder(TABLE_PROJECTTREE . '.endDate,' . TABLE_PROJECTTREE . '.name');
            break;
    }

    $time->setGroup('projectTree_id');
    if ($times = $time->getAll()) {
        $projectTree = modules_project_tree::getInstance();
        $largestDuration = 0;
        foreach ($times as $key => $aTime) {
            $times[$key]['_durationSum'] = $time->_calcDuration($aTime['durationSumSec'],'decimal');
            $times[$key]['_name'] = $projectTreeDyn->getPathAsString($aTime['projectTree_id']);
            // we dont want to divide by zero
            if (!empty($times[$key]['maxDuration'])) {
                $times[$key]['_percent'] = round(((float)$times[$key]['_durationSum'] / $times[$key]['maxDuration']) * 100, 2);
            }
            $times[$key]['_isProjectAvail'] = $projectTree->isAvailable($times[$key]['projectTree_id'], time());
        }

        // filter projects according selection
        $filteredTimes = array();
        foreach ($times as $key => $aTime) {
            switch ($filter) {
                case 'all':
                    $filteredTimes[$key] = $aTime;
                    $all = 'boldbutton';
                    $closed = $active = ''; 						
                    break;
                case 'closed':
                    if (!$times[$key]['_isProjectAvail']) {
                        $filteredTimes[$key] = $aTime;
                    }
                    $closed = 'boldbutton';
                    $active = $all = ''; 						
                    break;
                case 'active':
                default:
                    if ($times[$key]['_isProjectAvail']) {
                        $filteredTimes[$key] = $aTime;
                    }
                    $active = 'boldbutton';
                    $all = $closed = ''; 						
                    break;
            }
        }          
        $times = $filteredTimes;
        unset($filteredTimes);

        foreach ($times as $key => $aTime) {
            // AK : avoid php notices
            if (isset($times[$key]['_percent'])) {
                $times[$key]['_width'] = $times[$key]['_percent'];
            } else {
                $times[$key]['_width'] = 0;
            }
            foreach ($milestones as $aMilestone) {
                if (isset($times[$key]['_percent']) &&
                        ($times[$key]['_percent'] < $aMilestone['percentage'])) {
                    break;
                }
                $lastColor = $aMilestone['color'];
            }
            $times[$key]['_color'] = $lastColor;
        }
    } else {
        $applMessage->set('There are no projects with more than zero hours, that you are allowed to see.');
    }
}

require_once $config->finalizePage;
