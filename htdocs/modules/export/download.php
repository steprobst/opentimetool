<?php
/**
 * 
 * $Id: download.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/export/export.php';

if (empty($_REQUEST['id']) ) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect('index.php');
}

$export->putFile(intval($_REQUEST['id']), @$_REQUEST['download']);
