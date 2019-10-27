<?php
/**
 * 
 * $Id: index.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
require_once $config->classPath . '/modules/user/user.php';
require_once $config->classPath . '/modules/project/treeDyn.php';
require_once $config->classPath . '/modules/project/member.php';

if (isset($_POST['action_extendedFilter'])) {
    //if (isset($session->temp->time)) 
    $extendedFilter = @$session->temp->time->extendedFilter = !@$session->temp->time->extendedFilter;
    //else
    //    $extendedFilter = null;
    if (!$extendedFilter) {
        if (isset($_REQUEST['show']['projectTree_ids'])) {
            unset($_REQUEST['show']['projectTree_ids']);
        }
        if (isset($session->temp->time_index)) {
            unset($session->temp->time_index);
        }
    }
}

if (isset($session->temp->time)) {
    $extendedFilter = $session->temp->time->extendedFilter;
} else {
    $extendedFilter = null;
}
$show = &$session->temp->time_index;

// those two lines handle the edit functionality
$pageHandler->setObject($time);
// AK : if newData not in REQUEST, save returns an empty object
if (!$pageHandler->save(@$_REQUEST['newData'])) {
    $data = $pageHandler->getData();
    // convert the time and date, so the macro can show it properly ... do this better somehow
    if (isset($data['timestamp_date']) && isset($data['timestamp_time']) &&
            !isset($data['timestamp'])) {
        $_date = explode('.', $data['timestamp_date']);
        $_time = explode(':', $data['timestamp_time']);
        $data['timestamp'] = mktime(
            (int)$_time[0], (int)$_time[1], 0, (int)$_date[1], (int)$_date[0], (int)$_date[2]
        );
    }
}
// this handles the remove-functionality
if (!empty($_REQUEST['removeId'])) {
    $time->remove(intval($_REQUEST['removeId']));
}

// this takes care of saving the show-parameters in the session
// and retreiving them from there
if (isset($_REQUEST['show'])) {
    $show = $_REQUEST['show'];
}
if (!$show) {
    $show['user_id'] = $userAuth->getData('id');
}

if (isset($_REQUEST['action_showToday'])) {
    unset($show['humanDateFrom']);
    unset($show['humanDateUntil']);
}
// convert the dates and set the where-clauses
if (isset($show['humanDateFrom'])) {
    $dateFrom = explode('.', $show['humanDateFrom']);
}
if (isset($show['humanDateUntil'])) {
    $dateUntil = explode('.', $show['humanDateUntil']);
}
if (isset($dateFrom)) {
    $show['dateFrom'] = mktime(
        0, 0, 0, (int)$dateFrom[1], (int)$dateFrom[0], $dateFrom[2]?(int)$dateFrom[2]:date('Y')
    );
} else {
    $show['dateFrom'] = time();
}
if (isset($dateUntil)) {
    $show['dateUntil'] = mktime(
        0, 0, 0, (int)$dateUntil[1], (int)$dateUntil[0], $dateUntil[2]?(int)$dateUntil[2]:date('Y')
    );
} else {
    $show['dateUntil'] = time();
}
// handle the buttons
if (isset($_REQUEST['action_showDayPlus1'])) {
    $show['dateUntil'] = $show['dateUntil'] + 60*60*24;
    $show['dateFrom'] = $show['dateFrom'] + 60*60*24;
}
if (isset($_REQUEST['action_showWeekPlus1'])) {
    $show['dateUntil'] = $show['dateUntil'] + 60*60*24*7;
    $show['dateFrom'] = $show['dateFrom'] + 60*60*24*7;
}
if (isset($_REQUEST['action_showDayMinus1'])) {
    $show['dateUntil'] = $show['dateUntil'] - 60*60*24;
    $show['dateFrom'] = $show['dateFrom'] - 60*60*24;
}
if (isset($_REQUEST['action_showWeekMinus1'])) {
    $show['dateUntil'] = $show['dateUntil'] - 60*60*24*7;
    $show['dateFrom'] = $show['dateFrom'] - 60*60*24*7;
}
// convert the dateUntil and dateFrom back to humanDate... so this is saved in the session too
$show['humanDateFrom'] = date('d.m.Y', $show['dateFrom']);
$show['humanDateUntil'] = date('d.m.Y', $show['dateUntil']);

// set the authenticated user's id if none was chosen in the frontend
$isManager = $projectMember->isManager();
if (empty($show['user_ids']) || !$isManager) {
    // empty the array and show only this users data!!!
    $show['user_ids'] = array();
    $show['user_ids'][0] = $userAuth->getData('id');
}

$curUserId = $userAuth->getData('id');
$isAdmin = $user->isAdmin();

// getFiltered uses only some of the data in the show-array,
// but exactly those we need :-)
// it takes care of stuff like user_ids, projectTree_ids, etc.
$show['timestamp_start'] = $show['dateFrom'];
$show['timestamp_end'] = $show['dateUntil'];

if ($_times = $time->prepareResult($time->getFiltered($show))) {
    $_lastDate = 0;
    $times = array();
    foreach ($_times as $aTime) {
        $_date = date('dmY', $aTime['timestamp']);
        if ($_date != $_lastDate && !empty($aDayTimes)) {
            //$times = array_merge($times,array_reverse($aDayTimes));
            foreach (array_reverse($aDayTimes) as $val) {
                $times[] = $val;
            }
            $aDayTimes = array();
        }
        // set an additional field, which we can check in the
        // template to know if the current user can edit this entry
        $aTime['_canEdit'] = ($isAdmin || $curUserId == $aTime['_user_id']);
        $aDayTimes[] = $aTime;
        $_lastDate = $_date;
    }
    //$times = array_merge($times,array_reverse($aDayTimes)); // add the last day too :-)
    foreach (array_reverse($aDayTimes) as $val) {
        $times[] = $val;
    }
}

/**
 * We'll get here when using the export button.
 * "Nach Datum" und "Export" time/index.php is called
 * where you can find this button
 * 
 * lets get the number of exported files and show at most 10 of them
 * and adapt the size of the window, i.e. if only 3 are visible the window will
 * be so big that 3 items will be properly shown without scroll :-) nice little feature
 */
$exportWinHeight = 410;
if (isset($_REQUEST['action_export'])) {
    require_once $config->classPath . '/modules/export/export.php';
    $expCount = $export->getCount();
    $exportWinHeight += (min(10, $expCount) * 30);
}

$tasks = $task->getAll();
$users = $user->getAllAvail();
$projectTreeJsFile = 'projectTree' . ($isAdmin ? 'Admin' : '');

require_once $config->finalizePage;
