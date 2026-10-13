<?php
include_once __DIR__ . '/../db/conn.php';

class Controller
{
    public $conn;

    public function __construct()
    {
        $this->conn = $GLOBALS["conn"];
    }

    public function addJelentkezo($_teljesNev, $szakNev, $iskola, $kollegium, $last_year)
    {
        $sql = "INSERT INTO `ntszki`.`jelentkezok`(`teljesNev`, `szakNev`, `iskola`, `kollegium`, `last_year`) VALUES('" . $_teljesNev . "', '" . $szakNev . "', '" . $iskola . "', " . $kollegium . ", " . $last_year . ");";
        $result = $this->conn->query($sql);

        return $result;

    }

    public function deleteJelentkezo($id)
    {
        $sql = "DELETE FROM `ntszki`.`jelentkezok` WHERE `id` = " . $id . ";";
        $result = $this->conn->query($sql);

        return $result;

    }

    public function getAllJelentkezo($vegzos)
    {
        if ($vegzos == 'true') {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok` WHERE `last_year` = 1;";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok`;";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;
        }

    }

    public function getAllKolisJelentkezo($vegzos)
    {
        if ($vegzos == 'true') {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok` WHERE `kollegium` = 1 AND `last_year` = 1;";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;

        } else {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok` WHERE `kollegium` = 1;";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;
        }

    }

    public function getAllSzakJelentkezo($szak, $vegzos)
    {
        if ($vegzos == 'true') {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok` WHERE `szakNev` = '" . $szak . "' AND `last_year` = 1";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;

        } else {
            $sql = "SELECT * FROM `ntszki`.`jelentkezok` WHERE `szakNev` = '" . $szak . "'";
            $result = $this->conn->query($sql);
            $rows = [];
            foreach ($result as $row) {
                $rows[] = $row;
            }
            return $rows;
        }

    }
}

?>