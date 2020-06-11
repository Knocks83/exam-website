<?php
class Api
{
    // DB Connection
    private $conn;

    private $table_name;    // The name of the table the instance is using
    public $cols = [];      // The list of columns with their type

    /**
     * Class constructor
     * 
     * @param db The PDO object
     * @param table_name The table to use
     */
    public function __construct($db, $table_name)
    {
        include '../config.php';

        $this->conn = $db;
        if ($table_name == NULL)
            $this->table_name = $default_table;
        else
            $this->table_name = $table_name;

        $this->changeTable($table_name);
    }

    /**
     * Change the table used by the class
     * 
     * @param table_name The name of the table you want to use
     */
    function changeTable($table_name)
    {
        include '../config.php';

        if ($table_name == NULL)
            $this->table_name = $default_table;
        else
            $this->table_name = $table_name;

        $this->cols = [];
        $stmt = $this->conn->query('SELECT COLUMN_NAME, DATA_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA="' . $db_name . '" AND TABLE_NAME="' . $this->table_name . '"');

        $stmt->execute();
        $this->cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get DB schema
     * 
     * @return schema the DB schema
     */
    function schema()
    {
        $stmt = $this->conn->query('SHOW TABLES;');

        return $stmt->fetchAll();
    }

    /**
     * Get data from DB
     * 
     * @return stmt the PDO statement
     */
    function get()
    {
        // Prepare the query
        $stmt = $this->conn->query('SELECT * FROM ' . $this->table_name);

        return $stmt;
    }

    /**
     * Search data in DB
     * 
     * @param field the field where to search the data
     * @param input what to search
     * 
     * @return stmt the PDO statement
     */
    function search($field, $input)
    {
        // Prepare the query
        $stmt = $this->conn->query('SELECT * FROM ' . $this->table_name . " WHERE $field  = \"" . $input . '";');

        return $stmt;
    }
    function getCoords($city)
    {
        // Prepare the query
        $stmt = $this->conn->query('SELECT latitude, longitude
        FROM air_cities
        WHERE air_cities.city = "' . $city . '"');

        return $stmt;
    }

    /**
     * Delete a row
     * 
     * @param values An associative array containing column_name => values 
     * 
     * @return rowCount The number of edited rows
     */
    function addUpdate($values)
    {
        $query = 'INSERT INTO ' . $this->table_name;
        if (empty($values))
            return;

        $i = 0;
        $length = count($values);

        $keysStr = '(';
        $valuesStr = '(';
        $updateStr = '';

        foreach ($values as $key => $value) {
            $keyVal = htmlspecialchars(strip_tags($key));
            $valVal = htmlspecialchars(strip_tags($value));
            if ($i < $length - 1) {
                $keysStr .= $keyVal . ',';
                $valuesStr .= '"' . $valVal . '",';
                $updateStr .= $keyVal . ' = "' . $valVal . '", ';
            } else {
                $keysStr .= $keyVal . ')';
                $valuesStr .= '"' . $valVal . '")';
                $updateStr .= $keyVal . ' = "' . $valVal . '"';
            }
            $i++;
        }
        $query .= " $keysStr VALUES $valuesStr ON DUPLICATE KEY UPDATE $updateStr";

        unset($i, $keysStr, $valuesStr, $updateStr, $length);

        try {
            $this->conn->query($query);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a row
     * 
     * @param col The column where to search the value
     * @param value The value to search in the column
     * 
     * @return rowCount The number of edited rows
     */
    function delete($column, $value)
    {
        $query = 'DELETE FROM ' . $this->table_name . " WHERE $column = :value";
        $stmt = $this->conn->prepare($query);

        // Sanitize the input
        $value = htmlspecialchars(strip_tags($value));

        // Bind the variable and execute the query
        $stmt->bindParam('value', $value);

        if ($stmt->execute()) {
            return $stmt->rowCount();
        }

        return 0;
    }
}
