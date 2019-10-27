<?php
/**
 * 
 * $Id: popcalendar.css.php 119 2017-09-16 11:04:33Z munix9 $
 * 
 */

require_once '../../../config.php';

require_once 'HTTP/Header/Cache.php';

// compile the template (if needed), so we can check $tpl->compiled()
$tpl->compile($layout->getContentTemplate(__FILE__));

$httpCache = new HTTP_Header_Cache();
$httpCache->setHeader('Content-Type', 'text/css');

if (!$tpl->compiled()) {
    $httpCache->exitIfCached();
}

$httpCache->sendHeaders();

include $tpl->getCompiledTemplate();
