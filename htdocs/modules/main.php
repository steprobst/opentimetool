<?php
/**
 * 
 * $Id: main.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

$_openTimetool = $config->applName;

$canBeAdmin = $user->canBeAdmin();
if (!$config->demoMode && !is_array($canBeAdmin) && $canBeAdmin !== false) {
    $_openTimetool = '<span title="PHP version: ' . PHP_VERSION . '">'
                   . $_openTimetool . '</span>';
}

require_once $config->finalizePage;
