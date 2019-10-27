<?php

$sqlcheck = "SELECT id from `translate_en` where id=307";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(307, 'Delete All Exports', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=307";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(307, 'Alle Exports l&ouml;schen', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=308";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(308, 'Are you sure you want to delete all exports?', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=308";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(308, 'Wollen Sie wirklich alle Exports l&ouml;schen?', '0')";
$this->insert_record($sqlcheck, $sqlinsert);
