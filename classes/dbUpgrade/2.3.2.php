<?php

$sqlcheck = "SELECT id from `translate_en` where id=298";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(298, 'CAUTION: Project overbooked!', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=298";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(298, 'ACHTUNG: Projekt überbucht!', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=299";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(299, 'CAUTION: Only ', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=299";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(299, 'ACHTUNG: Nur ', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=300";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(300, 'hours left', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=300";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(300, 'Stunden übrig', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=301";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(301, 'Do you still want to book ?', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=301";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(301, 'Wollen Sie die Buchung trotzdem durchführen?', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=302";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(302, 'CANCEL : No booking!', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=302";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(302, 'Abrechen: Buchung wird nicht durchgeführt', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=303";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(303, 'OK : Booking will be done! (Project overbooked)', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=303";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(303, 'OK: Buchung wird durchgeführt, Projekt wird überbucht!', '0')";				
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_en` where id=304";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(304, 'Active projects', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=304";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(304, 'Aktive Projekte', '0')";				
$this->insert_record($sqlcheck, $sqlinsert);				

$sqlcheck = "SELECT id from `translate_en` where id=305";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(305, 'Closed projects', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=305";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(305, 'Geschlossene Projekte', '0')";				
$this->insert_record($sqlcheck, $sqlinsert);				

$sqlcheck = "SELECT id from `translate_en` where id=306";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES " .
             "(306, 'All projects', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id from `translate_de` where id=306";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES " .
             "(306, 'Alle Projekte', '0')";				
$this->insert_record($sqlcheck, $sqlinsert);				

// we switch of this much too short english word as it garbles often the translation
$sqlcheck = "SELECT id from `translate_en` where id=20";
$sqlinsert = "UPDATE `translate_en` SET `string`='allX' WHERE `id`=20";
$this->update_record($sqlcheck, $sqlinsert);
