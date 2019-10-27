<?php

$sqlcheck = "SELECT id from `translate_en` where id=297";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(297, 'There are no projects with more than zero hours, that you are allowed to see.', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=297";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(297, 'Es gibt keine Projekte mit gebuchten Stunden, die Sie sehen d&uuml;rfen.', '0')";
$this->insert_record($sqlcheck, $sqlinsert);
