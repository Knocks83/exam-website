<?php
class Api
{
    // DB Connection
    private $conn;

    private $table_name;
    public $cols = [];

    /**
     * Class constructor
     * 
     * @param db The PDO object
     * @param table_name The table to use
     */
    public function __construct($db, $table_name)
    {
        include 'config.php';

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
        include 'config.php';

        if ($table_name == NULL)
            $this->table_name = $default_table;
        else
            $this->table_name = $table_name;

        $this->cols = [];
        $stmt = $this->conn->query('SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA="' . $db_name . '" AND TABLE_NAME="' . $this->table_name . '"');

        $stmt->execute();
        foreach ($stmt->fetchAll() as $col) {
            array_push($this->cols, $col['COLUMN_NAME']);
        }
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
