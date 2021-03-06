<?php
/**
 * 
 * $Id: holiday.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
// 2.3.0 SX (AK) : we use the last used projectid if not set in form
// similar to shortcut.php
require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/project/member.php';

$data = array();

if (isset($_REQUEST['action_save'])) {
    $data = $_REQUEST['newData'];
    $data['startTime'] = $util->makeTimestamp($data['startTime']);
    $data['endTime']   = $util->makeTimestamp($data['endTime']);
    $data['startDate'] = $util->makeTimestamp($data['startDate']);
    $data['endDate']   = $util->makeTimestamp($data['endDate']);

    if ($data['startTime'] && $data['endTime'] && $data['startDate'] && $data['endDate']) {
        if ($data['startTime'] > $data['endTime']) {
            // switch the start and end time
            $temp = $data['startTime'];
            $data['startTime'] = $data['endTime'];
            $data['endTime'] = $temp;
            // so we have to switch the start and end tasks too!
            $temp = $data['startTask_id'];
            $data['startTask_id'] = $data['endTask_id'];
            $data['endTask_id'] = $temp;

            $applMessage->set('Your start time was later than the end time, they were switched!');
        }
        if ($data['startDate'] > $data['endDate']) {
            $temp = $data['startDate'];
            $data['startDate'] = $data['endDate'];
            $data['endDate'] = $temp;
            $applMessage->set('Your start date was after the end date, they were switched!');
        }

        $save['comment'] = $data['comment'];

        $starttask = $data['startTask_id'];

        $ourproject = $data['projectTree_id'];
        if (empty($ourproject) && $task->isNoneProjectTask($starttask)) {
            // 2.3.0 SX (AK) : we use the last used projectid if not set in form and task
            // is a task without project
            // similar to shortcut.php
            $time->reset();
            $time->setSelect('projectTree_id');
            $time->setWhere('user_id=' . $userAuth->getData('id'));
            $time->setOrder('timestamp', true);
            $lastTime = $time->getAll(0, 1);
            $projectId = $lastTime[0]['projectTree_id'];
            // check if the project is available, if not use root-id
            $projectTree = modules_project_tree::getInstance(true);
            if (!$projectId || !$projectTree->isAvailable($projectId, time())) {
                $availableProjects = $projectTree->getAllAvailable();
                if (is_array($availableProjects) && !empty($availableProjects)) {
                    foreach ($availableProjects as $aProject) {
                        if ($projectMember->isMember($aProject['id'])) {
                            $projectId = $aProject['id'];
                            break;
                        }
                    }
                }
            }
            // the default project if any
            $ourproject = $projectId;
        }

        $save['projectTree_id'] = $ourproject;
        //$save['user_id'] = $userAuth->getData('id');
        // AK: comes now from form (set below or selected if admin)
        $save['user_id'] = intval($_REQUEST['user_id']);

        // date('w') - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)
        $numDays = (($data['endDate'] - $data['startDate']) / (24 * 60 * 60) + 1);
        for ($i=0; $i < $numDays; $i++) {
            // get the day we are working on now
            $curDate = ($data['startDate'] + $i * 24 * 60 * 60);
            // check that it is no weekend day
            if (date('w', $curDate) == 0 || date('w', $curDate) == 6) {
                continue;
            }

            // save start task for this
            $save['timestamp_date'] = date('d.m.Y', $curDate);
            $save['timestamp_time'] = date('H:i', $data['startTime']);
            $save['task_id'] = $data['startTask_id'];
            if (!$time->save($save)) {
                $applError->setOnce('Error saving time for the ' . $save['timestamp_date'] . '!');
            } else {
                $applMessage->setOnce('Time(s) saved.');
            }

            // end task for that day
            $save['timestamp_time'] = date('H:i',$data['endTime']);
            $save['task_id'] = $data['endTask_id'];
            if (!$time->save($save)) {
                $applError->setOnce('Error saving time for the ' . $save['timestamp_date'] . '!');
            } else {
                $applMessage->setOnce('Time(s) saved.');
            }
        }
    } else {
        $applError->set('Please define a start and end date for your holiday period!');
    }
}

if (empty($data)) {
    $data['startTime'] = 60 * 60 * 8;  // use 9:00 as default
    $data['endTime']   = 60 * 60 * 16; // and 17:00 as end default, those are 8 hours
    $data['startDate'] = time();       // use today as start date
    $data['endDate']   = time();
}

// start task needs to be a task that calc's time
$task->setWhere('calcTime<>0');
$tasks = $task->getAll();

// the endtask can only be a task that doesnt get calculated
$task->setWhere('calcTime=0');
$endTasks = $task->getAll();

$isAdmin = $user->isAdmin();
$projectTreeJsFile = 'projectTree' . ($isAdmin?'Admin':'');

// AK: fill select form if admin
if ($isAdmin) {
    $user->setOrder('surname,name', true);
    $users = $user->getAll();
}

$userId = (!empty($_REQUEST['user_id'])) ? intval($_REQUEST['user_id']) : 0;
if (empty($userId)) {
    $userId = $userAuth->getData('id');
}

require_once $config->finalizePage;
