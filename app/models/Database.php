<?php

# PDO Wrapper, supporting MySQL and Sqlite
# Usage:
#   $db = new db();
#
#   // table, data
#   $db->create('users', array(
#     'fname' => 'john',
#     'lname' => 'doe'
#   ));
#   
#   // table, where, where-bind
#   $db->read('users', "fname LIKE :search", array(
#     ':search' => 'j%'
#   ));
#
#   // table, data, where, where-bind
#   $db->update('users', array(
#     'fname' => 'jame'
#   ), 'gender = :gender', array(
#     ':gender' => 'female'
#   ));
#   
#   // table, where, where-bind
#   $db->delete('users', 'lname = :lname', array(
#     ':lname' => 'doe'
#   ));

class Database
{

    /**
     *
     * @var type 
     */
    private $config = [];

    /**
     *
     * @var type 
     */
    private $log;

    public function connect ()
    {
        $dbConfig = Phalcon\DI::getDefault ()['configuration']['database'];

        if ( $_SERVER['HTTP_HOST'] == "localhost" )
        {
            $config = $dbConfig['development'];
        }
        else
        {
            $config = $dbConfig['live'];
        }

        $this->config = array(
            "dbdriver" => "mysql",
            "dbuser" => $config['username'],
            "dbpass" => $config['password'],
            "dbname" => $config['dbname'],
            "dbhost" => $config['host']
        );

        $now = new DateTime();
        $mins = $now->getOffset () / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs ($mins);
        $hrs = floor ($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf ('%+d:%02d', $hrs * $sgn, $mins);

        $logFile = $_SERVER['DOCUMENT_ROOT'] . "/blab/app/logs";

        $this->log = new Katzgrau\KLogger\Logger ($logFile, Psr\Log\LogLevel::DEBUG, ["filename" => "debug_" . date ('m-d-Y') . ".log"]);

        $dbhost = $this->config['dbhost'];
        $dbuser = $this->config['dbuser'];
        $dbpass = $this->config['dbpass'];
        $dbname = $this->config['dbname'];
        # $sqlitedb = $this->config['sqlitedb'];
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
            switch ($this->config["dbdriver"]) {
                case "sqlite":
                    $conn = "sqlite:{$sqlitedb}";
                    break;
                case "mysql":
                    $conn = "mysql:host={$dbhost};dbname={$dbname}";
                    break;
                default:
                    echo "Unsuportted DB Driver! Check the configuration.";
                    exit (1);
            }
            $this->db = new PDO ($conn, $dbuser, $dbpass, $options);
            $this->db->exec ("SET time_zone='$offset';");
        } catch (PDOException $e) {
            throw new Exception ($e->getMessage ());
        }
    }

    /**
     * 
     * @param type $table
     * @param type $where
     * @param type $bind
     * @param type $fields
     * @param type $orderBy
     * @param type $limit
     * @return boolean
     */
    public function _select ($table, $where = "", $bind = array(), $fields = "*", $orderBy = '', $limit = null)
    {
        $sql = "SELECT " . $fields . " FROM " . $table;
        if ( !empty ($where) )
        {
            $sql .= " WHERE " . $where;
        }

        if ( trim ($orderBy) !== "" )
        {
            $sql .= " ORDER BY " . $orderBy;
        }

        if ( $limit !== null && is_int ($limit) )
        {
            $sql .= " LIMIT " . $limit;
        }

        try {
            $this->log->info ($sql);

            $result = $this->run ($sql, $bind);

            if ( $result === false )
            {
                return false;
            }

            $result->setFetchMode (PDO::FETCH_ASSOC);
            $rows = array();

            while ($row = $result->fetch ()) {
                $rows[] = $row;
            }
            return $rows;
        } catch (PDOException $ex) {
            $this->log->emergency ($ex->getMessage ());
            return false;
        }
    }

    /**
     * 
     * @param type $table
     * @param type $data
     * @param type $where
     * @param type $bind
     * @return type
     */
    public function update ($table, $data, $where, $bind = array())
    {
        $fields = $this->filter ($table, $data);
        $fieldSize = sizeof ($fields);
        $sql = "UPDATE " . $table . " SET ";

        for ($f = 0; $f < $fieldSize; ++$f) {
            if ( $f > 0 )
            {
                $sql .= ", ";
            }

            $sql .= $fields[$f] . " = :update_" . $fields[$f];
        }

        $sql .= " WHERE " . $where . ";";

        foreach ($fields as $field) {
            $bind[":update_$field"] = $data[$field];
        }

        $this->log->info ($sql);

        try {
            $result = $this->run ($sql, $bind);
            return $result->rowCount ();
        } catch (PDOException $ex) {
            $this->log->emergency ($ex->getMessage ());
        }
    }

    /**
     * 
     * @param type $table
     * @param type $where
     * @param type $bind
     */
    public function delete ($table, $where, $bind = array())
    {
        $sql = "DELETE FROM " . $table;


        $sql .= " WHERE " . $where . ";";

        try {
            $this->log->info ($sql);
            $result = $this->run ($sql, $bind);
            
            if($result === false) {
                return false;
            }
            
        } catch (PDOException $ex) {
            $this->log->emergency ($ex->getMessage ());
        }
    }

    /**
     * 
     * @param type $table
     * @param type $data
     * @return boolean
     */
    public function create ($table, $data)
    {
        $fields = $this->filter ($table, $data);
        $sql = "INSERT INTO " . $table . " (" . implode ($fields, ", ") . ") VALUES (:" . implode ($fields, ", :") . ");";

        $bind = array();
        foreach ($fields as $field) {
            $bind[":$field"] = $data[$field];
        }
        $result = $this->run ($sql, $bind);

        $this->log->info ($sql);
        $this->log->info (json_encode ($bind));

        if ( $result === false )
        {
            return false;
        }

        return $this->db->lastInsertId ();
    }

    /**
     * 
     * @param type $sql
     * @param type $bind
     * @param type $create
     * @return boolean
     */
    public function _query ($sql, $bind = array(), $create = false)
    {

        $result = $this->run ($sql, $bind);

        $this->log->info ($sql);
        $this->log->info (json_encode ($bind));

        if ( $result === false )
        {
            trigger_error ('failed to run query', E_USER_WARNING);
            return false;
        }

        try {
            if ( $create === false )
            {
                $result->setFetchMode (PDO::FETCH_ASSOC);
                $rows = array();

                while ($row = $result->fetch ()) {
                    $rows[] = $row;
                }
                return $rows;
            }
        } catch (PDOException $ex) {
            
        }



        return true;
    }

    /**
     * 
     * @param type $sql
     * @param type $bind
     * @return type
     */
    private function run ($sql, $bind = array())
    {
        $sql = trim ($sql);

        try {
            $result = $this->db->prepare ($sql);
            $result->execute ($bind);
            return $result;
        } catch (PDOException $e) {
            $this->log->emergency ($e->getMessage ());
            return false;
        }
    }

    /**
     * 
     * @param type $table
     * @param type $data
     * @return type
     */
    private function filter ($table, $data)
    {
        $driver = $this->config['dbdriver'];
        if ( $driver == 'sqlite' )
        {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        }
        elseif ( $driver == 'mysql' )
        {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        }
        else
        {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }
        if ( false !== ($list = $this->run ($sql)) )
        {
            $fields = array();
            foreach ($list as $record)
                $fields[] = $record[$key];
            return array_values (array_intersect ($fields, array_keys ($data)));
        }
        return array();
    }

}
