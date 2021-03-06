<?php
/**
 * 
 * $Id: processOOoFile.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

function OOencode($string)
{
    // SX : required on php5.3 when we have to use default_charset = utf-8 (config.php)
    $dcs = ini_get('default_charset');
    if ($dcs != 'utf-8') {
        return utf8_encode(str_replace("\n", '<text:line-break />',
                htmlspecialchars($string))); // HS
    }
    return str_replace("\n", '<text:line-break />', htmlspecialchars($string)); // HS
}

//FIXXXXXME go thru all fields properly and OOencode them, put OOencode in PEAR or in $util, or in HTML/Template/Xipe

// get the data to fill the template
require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';

// so we dont get no php-errors in the template, which might make it invalid
// i.e if a foreach has no values to go thru
ini_set('display_errors', 0);

$options = array(
    'templateDir' => $config->applRoot,
    'compileDir'  => 'tmp', // use the compile dir 'tmp' under the tempalte dir
    'verbose'     => false, // this is default too
    'logLevel'    => 0,     // dont write log files
    'filterLevel' => 9,     // apply all the most common filters
    'locale'      => 'en',
    'autoBraces'  => false, // we use foreach endforeach here
);
$tpl = new HTML_Template_Xipe($options);
$show = $session->temp->time_index;

// you can switch on debugging here. Output is written to a file on server
$f = null;
$dbg = false;
if ($dbg) {
    $f = fopen($config->applRoot . '/tmp/ooexport.txt', 'wb');
}

// copy from time/index.php!!!!
// set the authenticated user's id if none was chosen in the frontend
$isManager = $projectMember->isManager();
if (!$show['user_ids'] || !$isManager) {
    // empty the array and show only this users data!!!
    $show['user_ids'] = array();
    $show['user_ids'][0] = $userAuth->getData('id');
}

$time->preset();
if (!empty($show['comment'])) {
    $time->addWhereSearch(TABLE_TIME . '.comment', $show['comment']);
}
if (isset($show['projectTree_ids']) && is_array($show['projectTree_ids']) && sizeof($show['projectTree_ids'])) {
    $time->addWhere(TABLE_PROJECTTREE . '.id IN (' . implode(',', $show['projectTree_ids']) . ')');
}
if (isset($show['task_ids']) && is_array($show['task_ids']) && sizeof($show['task_ids'])) {
    $time->addWhere(TABLE_TASK . '.id IN (' . implode(',', $show['task_ids']) . ')');
}
if (isset($show['user_ids']) && is_array($show['user_ids']) && sizeof($show['user_ids'])) {
    $time->addWhere(TABLE_USER . '.id IN (' . implode(',', $show['user_ids']) . ')');
}

$times = $time->getDay($show['dateFrom'], $show['dateUntil']);
// copy end

$timespanFrom = OOencode($dateTime->formatDate($show['dateFrom']));
$timespanUntil = OOencode($dateTime->formatDate($show['dateUntil']));

// SX To get the previously booked times below
$until = $show['dateFrom'];

// 2.3.2 : some general info for template
$general['exportdate'] = date('d.m.Y');
$general['firstname'] = $userAuth->getData('name');
$general['lastname'] = $userAuth->getData('surname');

// put all the users in the first level of the array
$users = array();
$sum = 0;
foreach ($times as $aTime) {
    // only if there is a time for this task, add it to the sum ('Gehen' has no time)
//    if ($aTime['_task_calcTime']) {
        $curUserId = $aTime['user_id'];
        // if this user has not been found yet, set his username
        if (!isset($users[$curUserId])) {
            $users[$curUserId]['name'] = OOencode($aTime['_user_name']);
            $users[$curUserId]['surname'] = OOencode($aTime['_user_surname']);
            $users[$curUserId]['sum'] = 0;
        }

        // only if there is a time for this task, put it in the array ('Gehen' has no time)
        if ($aTime['_task_calcTime']) {
            $aTime['duration'] = $time->_calcDuration($aTime['durationSec'], 'decimal');
        }

        $aTime['task'] = OOencode($aTime['_task_name']);
        $users[$curUserId]['days'][] = $aTime;
        // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
        if ($aTime['_task_calcTime']) {
            $users[$curUserId]['sum'] += $aTime['durationSec'];
        }

        // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
        if ($aTime['_task_calcTime']) {
            $sum += $aTime['durationSec'];
        }
//    }
}

// this is the overall sum !! Can be used in Template as $sum, $sumdays
$tsum = $sum;
$sum = $time->_calcDuration($tsum, 'decimal');
$sumdays = $time->_calcDuration($tsum, 'days');

if ($dbg) {
    fwrite($f, 'Global: ' . $tsum . ' sumdec= ' . $sum . ' sumdays= ' . $sumdays);
}

foreach ($users as $key => $aUser) {
    $newDays = array(); // so the last user doesnt get all the times
    foreach (array_reverse($aUser['days']) as $aTime) {
        $aTime['comment'] = OOencode($aTime['comment']);

        $dayIndex = date('dmY',$aTime['timestamp']);
        if (!isset($newDays[$dayIndex])) {
            $newDays[$dayIndex]['sum'] = 0;
        }

        // calc day sum
        // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
        if ($aTime['_task_calcTime']) {
            $newDays[$dayIndex]['sum'] += $aTime['durationSec'];
        }            

        $newDays[$dayIndex]['times'][] = $aTime;

        $newDays[$dayIndex]['date'] = OOencode($dateTime->formatDate($aTime['timestamp']));
        $newDays[$dayIndex]['dateShort'] = OOencode($dateTime->formatDateShort($aTime['timestamp']));
        $newDays[$dayIndex]['dateLong'] = OOencode($dateTime->formatDateLong($aTime['timestamp']));
        $newDays[$dayIndex]['dateFull'] = OOencode($dateTime->formatDateFull($aTime['timestamp']));
    }
    foreach ($newDays as $dkey => $newTimeswithSum) {
        $tsum = $newDays[$dkey]['sum'];
        $newDays[$dkey]['sum'] = $time->_calcDuration($tsum, 'decimal');
        $newDays[$dkey]['sumdays'] = $time->_calcDuration($tsum, 'days');
    }
    $users[$key]['days'] = $newDays;

    // this is the sum of user in decimal notation
    $tsum = isset($pusers[$key]['sum']) ? $pusers[$key]['sum'] : 0;
    $pusers[$key]['sum'] = $time->_calcDuration($tsum, 'decimal');
    $pusers[$key]['sumdays'] = $time->_calcDuration($tsum, 'days');
}

/**
 * now build the project view array for ootemplating (in principle just one level around the stuff above)
 * with sums for each project and each user 
 */
$projects = array();

// build the project array and calc the project sums in sec
foreach ($times as $aTime) {
    $curProjectId = $aTime['_projectTree_id'];
    // if this project has not been found yet, set its name
    if (!isset($projects[$curProjectId])) {
        $projects[$curProjectId]['projectname'] = OOencode($aTime['_projectTree_name']);
        $projects[$curProjectId]['projectcomment'] = OOencode($aTime['_projectTree_comment']);
        $projects[$curProjectId]['sum'] = 0;
        $projects[$curProjectId]['sumdays'] = 0;
        $booked_before_sec = SumBookedTimes($curProjectId, $until, $f, false);
        $booked_before_hours = (float) str_replace(
                ',', '.', $time->_calcDuration($booked_before_sec, 'decimal'));          
        $projects[$curProjectId]['sumbefore'] = $booked_before_hours; // SX March 2013 bug
        $projects[$curProjectId]['sumbeforedays'] = $time->_calcDuration($booked_before_sec, 'days');
        $projects[$curProjectId]['maxduration'] = $aTime['_projectTree_maxDuration']; // in h
        $maxdursec = $aTime['_projectTree_maxDuration'] * 3600;
        $projects[$curProjectId]['maxdurationdays'] = $time->_calcDuration($maxdursec, 'days');
    }   
    // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
    if ($aTime['_task_calcTime']) {
        $projects[$curProjectId]['sum'] += $aTime['durationSec'];
    }
}

// now we need to have the same stuff as for user array above ... per project
foreach ($projects as $key => $project) {
    $curProjectId = $key;
    // put all the users in the first level of the array
    $pusers = array();
    foreach ($times as $aTime) {
        if ($dbg) {
            //fwrite($f, "cpid=" . $curProjectId . ' == ' . $aTime['_projectTree_id']);
        }	
        if ($curProjectId == $aTime['_projectTree_id']) {
            $curUserId = $aTime['user_id'];
            // if this user has not been found yet, set his username
            if (!isset($pusers[$curUserId])) {
                $pusers[$curUserId]['name'] = OOencode($aTime['_user_name']);
                $pusers[$curUserId]['surname'] = OOencode($aTime['_user_surname']);
                $pusers[$curUserId]['sum'] = 0;
            }

            // only if there is a time for this task, put it in the array ('Gehen' has no time)
            if ($aTime['_task_calcTime']) {
                $aTime['duration'] = $time->_calcDuration($aTime['durationSec'], 'decimal');
            }

            $aTime['task'] = OOencode($aTime['_task_name']);
            $pusers[$curUserId]['days'][] = $aTime;
            // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
            if ($aTime['_task_calcTime']) {
                $pusers[$curUserId]['sum'] += $aTime['durationSec'];
            }
        }
    }

    foreach ($pusers as $key => $aUser) {
        $newDays = array(); // so the last user doesnt get all the times
        foreach (array_reverse($aUser['days']) as $aTime) {
            $aTime['comment'] = OOencode($aTime['comment']);

            $dayIndex = date('dmY', $aTime['timestamp']);
            if (!isset($newDays[$dayIndex])) {
                $newDays[$dayIndex]['sum'] = 0;
            }

            // calc day sum
            // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
            if ($aTime['_task_calcTime']) {
                $newDays[$dayIndex]['sum'] += $aTime['durationSec'];
            }

            $newDays[$dayIndex]['times'][] = $aTime;

            $newDays[$dayIndex]['date'] = OOencode($dateTime->formatDate($aTime['timestamp']));
            $newDays[$dayIndex]['dateShort'] = OOencode($dateTime->formatDateShort($aTime['timestamp']));
            $newDays[$dayIndex]['dateLong'] = OOencode($dateTime->formatDateLong($aTime['timestamp']));
            $newDays[$dayIndex]['dateFull'] = OOencode($dateTime->formatDateFull($aTime['timestamp']));
        }

        if ($dbg) {
            //fwrite($f, "newDays:" . print_r($newDays, true));
        }

        // transform the sum from sec to dec hours 4 all newDays
        foreach ($newDays as $dkey => $newTimeswithSum) {
            $tsum = $newDays[$dkey]['sum'];
            $newDays[$dkey]['sum'] = $time->_calcDuration($tsum, 'decimal');
            $newDays[$dkey]['sumdays'] = $time->_calcDuration($tsum, 'days');
        }

        $pusers[$key]['days'] = $newDays;

        // this is the sum of user in decimal notation
        $tsum = $pusers[$key]['sum'];
        $pusers[$key]['sum'] = $time->_calcDuration($tsum, 'decimal');
        $pusers[$key]['sumdays'] = $time->_calcDuration($tsum, 'days');
    }

    $projects[$curProjectId]['users'] = $pusers;

    if ($dbg) {
        fwrite($f, 'Pusers:' . print_r($pusers, true));
    }

    // this is the project sum in decimal notation
    $tsum =  $projects[$curProjectId]['sum'];
    $projects[$curProjectId]['sum'] = $time->_calcDuration($tsum, 'decimal');
    $projects[$curProjectId]['sumdays'] = $time->_calcDuration($tsum, 'days');
    // Bugfix 2.3.2.3 Mar 2013 : The previously booked times have to be included in rest calc
    $booked_before_hours = $projects[$curProjectId]['sumbefore'];
    // Bugfix 2.3.2.2 : Rest was wrong as descimal places were always cut off
    $sumf = (float) str_replace(',', '.', $projects[$curProjectId]['sum']);
    $rest = $projects[$curProjectId]['maxduration'] - $booked_before_hours - $sumf;
    $restsec = $rest * 3600;
    $projects[$curProjectId]['rest'] = $rest;
    $projects[$curProjectId]['restdays'] = $time->_calcDuration($restsec, 'days');
    $totalbookedhours = $booked_before_hours + $sumf;
    $totalbookedsec = $totalbookedhours * 3600;
    $projects[$curProjectId]['totalbooked'] = $totalbookedhours;
    $projects[$curProjectId]['totalbookeddays'] = $time->_calcDuration($totalbookedsec, 'days');
}

if ($dbg) {
    fwrite($f, '***Times:' . print_r($times, true));
    fwrite($f, '***Users:' . print_r($users, true));
    fwrite($f, '***Projects:' . print_r($projects, true));
    fclose($f);
}

/*
if (!$users[$aTime['user_id']]) {
    $users[$aTime['user_id']]['username'] = $aTime['_user_name'] . ' ' . $aTime['_user_surname'];
    $users[$aTime['user_id']]['email'] = $aTime['email'];
}
$users[$aTime['user_id']]['days'][''] = $aTime;
*/

$tpl->compile($session->temp->OOoExport->xmlFile);
if ($dbg) {
    include $tpl->getCompiledTemplate();
} else {
    @include $tpl->getCompiledTemplate();
}

// don't activate. Will be written into content.xml ...
//require_once $config->finalizePage;

/**
 * End of script; helpers functions follwing
 */

/**
 * Get all booked times on given project
 * 
 * Ak, system worx : We need that for the project sums 
 */
function AllBookedTimes($dataID)
{
    global $projectTree, $task;

    $ret = array();

    $time = new modules_common(TABLE_TIME);
    $time->reset();
    $time->setWhere('projectTree_id=' . $dataID);
    $time->setOrder('timestamp', true);
    if ($time->getCount()) {
        // AK, system worx : retrieve all logged times for that project
        $result = $time->GetAll();
        foreach ($result as $data) {
            $ret[$data['timestamp']] = $data;
        }
    }
    unset($time);

    return $ret;
}

/**
 * Get a sum of all booked times on given project until given date
 * 
 * Ak, system worx : We need that for the project sums
 */
function SumBookedTimes($projectId, $until, $f, $dbg)
{
    global $projectTree, $task;

    $bookedTimes = AllBookedTimes($projectId);

    $sum = 0;

    foreach ($bookedTimes as $data) {
        if ($dbg) {
            fwrite($f, 'times ' . print_r($data, true) . PHP_EOL);
        }
        if ($data['timestamp'] <= $until) {
            if (!$task->isNoneProjectTask($data['task_id'])) {
                $sum += $data['durationSec'];
            }
        }
    }
    if ($dbg) {
        fwrite($f, 'Sum ' . $sum . PHP_EOL);
    }
    return $sum;
}
