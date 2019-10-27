<?php
/**
 * 
 * $Id: index.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/task/task.php';
require_once 'vp/Application/HTML/NextPrev.php';

if (!$user->isAdmin()) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect($config->home);
}

if (!empty($_REQUEST['removeId'])) {
    $task->remove(intval($_REQUEST['removeId']));
}

$pageHandler->setObject($task);
if (!$pageHandler->save(@$_REQUEST['newData'])) {
    $data = $pageHandler->getData();
}

$task->preset();
$task->setWhere();
$nextPrev = new vp_Application_HTML_NextPrev($task);
$nextPrev->setLanguage($lang);
$tasks = $nextPrev->getData();

require_once $config->finalizePage;
