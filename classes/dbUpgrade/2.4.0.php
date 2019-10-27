<?php

$sql = "ALTER TABLE `projectTree` ADD INDEX `idLR` (`id`, `l`, `r`)";
$res = mysqli_query($this->conn, $sql);
if ($res === false) {
    die('Upgrade failed : ' . mysqli_error($this->conn));
}
