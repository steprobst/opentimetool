<?php
/**
 * 
 * $Id: adminMode.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

if (!empty($_REQUEST['adminModeOn'])) {
    $user->adminModeOn();
} else {
    $user->adminModeOff();
}

$isAdmin = $user->isAdmin();

require_once $config->finalizePage;
