<?php
/**
 * 
 * $Id: frameSet.php 119 2017-09-16 11:04:33Z munix9 $
 * 
 */

$session->layout = 'framedDefault';
$layout->setLayout('framedDefault');

$tpl->compile($layout->getContentTemplate());
// and include the compiled main template
include $tpl->compiledTemplate;
