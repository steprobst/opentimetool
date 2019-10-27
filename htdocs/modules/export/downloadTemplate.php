<?php
/**
 * 
 * $Id: downloadTemplate.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/OOoTemplate/OOoTemplate.php';

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    die();
}

$OOoTemplate->putFile(intval($_REQUEST['id']));
