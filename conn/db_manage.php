<?php
class db_manage
{
    public $db;    // DB Instance
    public $error; // Error Message

    private $db_serv = "localhost";
    private $db_user = "root";
    private $db_pass = "";
    private $db_schema = "projmgnt_db";

    /**
     * Constructor Function
     * @param string|null $inServ
     */
    function __construct($inServ = null)
    {
        global $DB_SERVER, $DB_PASSWORD;

        $this->db_serv = $DB_SERVER ?: $this->db_serv;
        $this->db_pass = $DB_PASSWORD ?: $this->db_pass;

        $server = $inServ ?: $this->db_serv;
        $this->db = mysqli_connect($server, $this->db_user, $this->db_pass, $this->db_schema);

        if ($this->db === false) {
            $this->error = "DB Connection Error: " . mysqli_connect_error();
            $this->db = null;
            return;
        }

        mb_language("uni");
        mb_internal_encoding("utf-8");
        mb_http_output("utf-8");
        $sql = "SET NAMES utf8mb4";
        mysqli_query($this->db, $sql);
    }

    /**
     * Run SQL String Query
     * @param string $insql The SQL Query String
     * @return array|string Array of selected column values or error message
     */
    function exec_query($insql)
    {
        $res = mysqli_query($this->db, $insql);

        if (mysqli_errno($this->db) != 0) {
            $error = mysqli_error($this->db);
            return $error;
        }

        $ret = [];

        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
            $ret[] = $row;
        }

        return $ret;
    }

    /**
     * Run SQL String Query for Execution (UPDATE, DELETE, INSERT, etc. commands)
     * @param string $insql The SQL Query String
     * @return int|string 0 if true; otherwise returns string MySQL error
     */
    function exec_cmd($insql)
    {
        $res = mysqli_query($this->db, $insql);

        if (mysqli_errno($this->db) != 0) {
            $error = mysqli_error($this->db);
            return $error;
        }

        return 0;
    }

    /**
     * Get the last ID INSERT Command ID.
     * @return int Last ID
     */
    function get_last_id()
    {
        return mysqli_insert_id($this->db);
    }

    /**
     * Close the database connection.
     */
    function close_connection()
    {
        if ($this->db !== null) {
            mysqli_close($this->db);
        }
    }
}
