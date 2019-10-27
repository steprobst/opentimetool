<?php

$sqlcheck = "SELECT id from `translate_en` where id=309";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(309, 'Please note! This function is disabled in the demo version.', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=309";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(309, 'Bitte beachten! Diese Funktion ist in der Demoversion deaktiviert.', '0')";
$this->insert_record($sqlcheck, $sqlinsert);
