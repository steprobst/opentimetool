<?php

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=7";
$sqlinsert = "UPDATE `translate_de` SET `string`='Bearbeiten' WHERE `id`=7";
$this->update_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=286";
$sqlinsert = "UPDATE `translate_de` SET `string`='Passwort zur&uuml;cksetzen' WHERE `id`=286";
$this->update_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=310";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(310, 'all projects', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=310";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(310, 'Alle Projekte', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=311";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(311, 'select project', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=311";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(311, 'Projekt ausw&auml;hlen', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=312";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(312, 'log in', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=312";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(312, 'Anmelden', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=313";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(313, 'send info mail', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=313";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(313, 'Info-Email senden', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=314";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(314, 'Please enter a task name!', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=314";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(314, 'Bitte eine Tätigkeit eingeben!', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=315";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(315, 'The task name already exists!', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=315";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(315, 'Die Tätigkeit existiert bereits!', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=316";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(316, 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.', 0)";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=316";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(316, 'Das Passwort sollte mindestens 8 Zeichen lang sein und mindestens einen Großbuchstaben, eine Zahl und ein Sonderzeichen enthalten.', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_en` WHERE id=317";
$sqlinsert = "INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES "
           . "(317, 'Change Password', '0')";
$this->insert_record($sqlcheck, $sqlinsert);

$sqlcheck = "SELECT id FROM `translate_de` WHERE id=317";
$sqlinsert = "INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES "
           . "(317, 'Passwort ändern', '0')";
$this->insert_record($sqlcheck, $sqlinsert);
