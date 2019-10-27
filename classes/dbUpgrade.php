<?php
/**
 * 'refactored' and extended by munix9
 * 
 * based on code by SX (AK) : Add DB upgrades here (Starting with V 2.3.0)
 * 
 * DB connect is done before. The calls here use that db connection by default
 * 
 * We introduce now a simple new one rec table to hold the schema info to avoid
 * an upgrade attempt during each server round trip !
 * The schema info MUST correspond with a new schema version string in config.php
 * 
 * NOTE : This works for mysql(i) only currently and doesn't make use of the pear interface
 * 
 * $Id$
 */

class dbUpgrade
{

    private $config;

    private $conn;

    public function __construct(&$config, &$db)
    {
        $this->config = $config;
        $this->conn = $db->connection;
    }

    public function run()
    {
        // first check and create if necessary (only once ;-) ...)
        $sqlc = "CREATE TABLE IF NOT EXISTS `schema_info` ( "
              . " `id` int(11) NOT NULL AUTO_INCREMENT, "
              . " `version` mediumtext NOT NULL, "
              . " PRIMARY KEY (`id`) "
              . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        $sqlvi = "INSERT INTO `schema_info` (`id`, `version`) VALUES (1, '2.3.0')";
        $initial_upgrade = false;

        //print_r($sqlc);
        $res = mysqli_query($this->conn, "SHOW TABLES LIKE 'schema_info'");
        if ($res === false) {
            $resc = mysqli_query($this->conn, $sqlc);
            if ($resc === false) {
                die("Can't upgrade database! " . mysqli_error($this->conn));
            } else {
                $resci = mysqli_query($this->conn, $sqlvi);
                if ($resci === false) {
                    die("Can't insert initial schema info " . mysqli_error($this->conn));
                }
            }
            $initial_upgrade = true;
        } else {
            $resa = mysqli_fetch_array($res);
            if (empty($resa)) {
                $resc = mysqli_query($this->conn, $sqlc);
                if ($resc === false) {
                    die("Can't upgrade database! " . mysqli_error($this->conn));
                } else {
                    $resci = mysqli_query($this->conn, $sqlvi);
                    if ($resci === false) {
                        die("Can't insert initial schema info " . mysqli_error($this->conn));
                    }
                }
                $initial_upgrade = true;
            }
            mysqli_free_result($res);
        }

        $sqlfv = "SELECT version FROM `schema_info` WHERE id=1";
        $resfv = mysqli_query($this->conn, $sqlfv);
        if ($resfv === false) {
            die("Can't fetch schema version ! " . mysqli_error($this->conn));
        }
        $db_schema_version = mysqli_fetch_array($resfv);
        $current_schema_version = $db_schema_version['version'];
        $wanted_schema_version  = $this->config->schema_version;
        mysqli_free_result($resfv);

        /**
         * Now a switch witout breaks : we enter at current schema version and walk
         * up until wanted schema version is reached (end of switch)
         */

        if ($initial_upgrade || ($current_schema_version != $wanted_schema_version)) {
            // Upgrade required

            mysqli_query($this->conn, "SET NAMES 'utf8'");

            switch ($current_schema_version) {

                case '2.3.0':
                    $this->upgrade_to('2.3.1');

                case '2.3.1':
                    $this->upgrade_to('2.3.2');

                case '2.3.2':
                    $this->upgrade_to('2.3.3');

                case '2.3.3':
                    $this->upgrade_to('2.3.4');

                case '2.3.4':
                    $this->upgrade_to('2.4.0');

                case '2.4.0':
                    $this->upgrade_to('2.7.0');

            } // switch end

            $ret = 1;
        } else {
            $ret = 0;
        }

        return $ret;
    }

    /**
     * checks first and then does the insert
     * 
     * @param string $sqlcheck
     * @param string $sqlinsert
     */
    private function insert_record($sqlcheck, $sqlinsert)
    {
        $rescheck = mysqli_query($this->conn, $sqlcheck);
        if ($rescheck === false) {
            die("Can't get info : " . mysqli_error($this->conn));
        }
        $res = mysqli_num_rows($rescheck);
        if (empty($res)) {
            $res = mysqli_query($this->conn, $sqlinsert);
            if ($res === false) {
                die('Upgrade failed : ' . mysqli_error($this->conn));
            }
        }
        mysqli_free_result($rescheck);
    }

    /**
     * checks first and then does the update
     * 
     * @param string $sqlcheck
     * @param string $sqlupdate
     */
    private function update_record($sqlcheck, $sqlupdate)
    {
        $rescheck = mysqli_query($this->conn, $sqlcheck);
        if ($rescheck === false) {
            die("Can't get info : " . mysqli_error($this->conn));
        }
        $res = mysqli_num_rows($rescheck);
        if (!empty($res)) {
            $res = mysqli_query($this->conn, $sqlupdate);
            if ($res === false) {
                die('Upgrade failed : ' . mysqli_error($this->conn));
            }
        }
        mysqli_free_result($rescheck);
    }

    /**
     * Upgrade schema version in db
     * 
     * @param string $schemaversion
     */
    private function update_schema_info($schemaversion)
    {
        $res = mysqli_query(
            $this->conn,
            "UPDATE `schema_info` SET `version`='" . $schemaversion . "' WHERE `id`=1"
        );
        if ($res === false) {
            die('Upgrade failed : ' . mysqli_error($this->conn));
        }
    }

    private function upgrade_to($version)
    {
        require_once $this->config->classPath . '/dbUpgrade/' . $version . '.php';
        $this->update_schema_info($version);
    }
}
