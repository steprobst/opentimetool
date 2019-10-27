<?php
/**
 * 
 * $Id: style.css.php 119 2017-09-16 11:04:33Z munix9 $
 * 
 */

require_once '../../config.php';

require_once 'HTTP/Header/Cache.php';

// compile the template (if needed), so we can check $tpl->compiled()
$tpl->compile($layout->getCssTemplate());

$httpCache = new HTTP_Header_Cache();
$httpCache->setHeader('Content-Type', 'text/css');
$httpCache->exitIfCached(!$tpl->compiled());

$httpCache->sendHeaders();

include $tpl->getCompiledTemplate();
