<?php
/**
 * 
 * $Id: header.php 121 2018-02-09 16:53:20Z munix9 $
 * 
 */

// SX Nov 2012 : xajax
require_once $config->applRoot . '/xajax/xajax_core/xajax.inc.php';
require_once $config->applRoot . '/modules/xajax_if.php';

$_htmlTitle = strip_tags($config->applName);

$bClass = array();
if (!$util->isDesktop()) {
    $bClass[] = 'mobile';
}
if (isset($GLOBALS['bodyClass'])) {
    $bClass[] = htmlentities($GLOBALS['bodyClass']);
}
$_bodyClass = implode(' ', $bClass);

require_once $config->finalizePage;
