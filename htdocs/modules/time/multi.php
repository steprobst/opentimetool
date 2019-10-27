<?php
/**
 * 
 * $Id: multi.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
require_once $config->classPath . '/modules/project/tree.php';

$projectTree = modules_project_tree::getInstance(true);

$data = array();
if (!empty($_REQUEST['newData'])) {
    $saved = 0;
    foreach ($_REQUEST['newData'] as $aNew) {
        if (!$aNew['timestamp_date'] || !$aNew['timestamp_time'] || !$aNew['task_id']) {
            continue;
        }
        $_data = $aNew;
        $_data['user_id'] = intval($_REQUEST['user_id']);
        if ($time->save($_data)) {
            $saved++;
        } else {
            $data[] = $_data;
        }
    }
    if ($saved > 0) {
        $applMessage->set("$saved datasets successfully saved.");
    }
    if (!empty($data)) {
        $applError->set('Please correct the data of the shown entries!');
    }
}

$isAdmin = $user->isAdmin();
if ($isAdmin) {
    $user->setOrder('surname,name', true);
    $users = $user->getAll();
}

$tasks = $task->getAll();
$allFolders = $projectTree->getAllAvailable();
$userId = (!empty($_REQUEST['user_id'])) ? intval($_REQUEST['user_id']) : 0;
if (empty($userId)) {
    $userId = $userAuth->getData('id');
}

require_once $config->finalizePage;
