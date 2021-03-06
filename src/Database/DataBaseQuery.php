<?php

/**
 * Class DataBase:
 * This class performs the basic CRUD operations which compose of
 * various methods such as create, read, update, and delete.
 * This class class query the database to achieve its function.
 *
 * @author: Raimi Ademola <ademola.raimi@andela.com>
 * @copyright: 2016 Andela
 */
namespace Demo;

use PDO;


class DataBaseQuery
{
    protected $tableName;
    protected $splitTableField;
    protected $formatTableValues;
    protected $dbConnection;

    /**
     * This is a constructor; a default method  that will be called automatically during class 
     * instantiation.
     */
    public function __construct($dbConn = null)
    {
        if (is_null($dbConn)) {
            $this->dbConnection = new DataBaseConnection();
        } else {
            $this->dbConnection = $dbConn;
        }
    }

    /**
     * This method create or insert new users to the table.
     *
     * @param $associativeArray
     * @param $tableName
     *
     * @return array
     */
    public function create($associativeArrayToCreate, $tableName)
    {
        $tableFields = [];
        $tableValues = [];

        foreach ($associativeArrayToCreate as $key => $val) {
            $tableFields[] = $key;
            $tableValues[] = $val;
        }
        
        $unexpectedArray = array_diff($tableFields, $this->getColumnNames($tableName));

        if (count($unexpectedArray) < 1) {
            $sql = 'INSERT INTO '.$tableName;
            $sql .= '('.$this->splitTableField($tableFields).') ';
            $sql .= 'VALUES ('.$this->formatTableValues($tableValues).')';

            $bool = $this->dbConnection->exec($sql);

            return $bool;
        }
        
        throw new FieldUndefinedException('Oops, '.$this->splitTableField($unexpectedArray).' is not defined as a field');
    }

    /**
     * This method read the data in the table name of the id being passed to it.
     *
     * @param $id
     * @param $tableName
     *
     * @return array
     */
    public static function read($id, $tableName, $dbConn = null)
    {
        if (is_null($dbConn)) {
            $dbConn = new DataBaseConnection();
        }

        $sql = $id ? 'SELECT * FROM '.$tableName.' WHERE id = '.$id : 'SELECT * FROM '.$tableName;
        $statement = $dbConn->prepare($sql);
        $statement->execute();
        
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) < 1) {
            throw new IdNotFoundException('Oops, the id '.$id.' is not in the table, try another id');
        }

        return $results;
    }

    /**
     * This method delete the table name of the id being passed to it.
     *
     * @param $update Params
     * @param $associativeArrayToUpdate
     * @param $tableName
     *
     * @return bool
     */
    public function update($updateParams, $associativeArrayToUpdate, $tableName)
    {
        $updateSql = "UPDATE `$tableName` SET ";

        unset($associativeArrayToUpdate['id']);

        foreach ($associativeArrayToUpdate as $key => $val) {
            $tableFields[] = $key;
        }

        $unexpectedArray = array_diff($tableFields, $this->getColumnNames($tableName));

        if (count($unexpectedArray) < 1) {
            $updateSql .= $this->updateArraySql($associativeArrayToUpdate);
            
            foreach ($updateParams as $field => $value) {
                $updateSql .= " WHERE $field = $value";
            }

            $statement = $this->dbConnection->exec($updateSql);
            
            return $statement;
        }

        throw new FieldUndefinedException('Oops, '.$this->splitTableField($unexpectedArray).' is not defined as a field');
    }

    /**
     * This method delete the table name of the id passed to it.
     *
     * @param $id
     * @param $tableName
     *
     * @return bool
     */
    public static function delete($id, $tableName, $dbConn = null)
    {
        if (is_null($dbConn)) {
            $dbConn = new DataBaseConnection();
        }

        $sql = 'SELECT * FROM '.$tableName.' WHERE id = '.$id;
        $statement = $dbConn->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) < 1) {
            throw new IdNotFoundException('Oops, the id '.$id.' is not in the database, try another id');
        }

        $sql = 'DELETE FROM '.$tableName.' WHERE id = '.$id;
        $statement = $dbConn->exec($sql);

        return true;
    }

    /**
     * This method returns a string form an array by making us of the implode function.
     *
     * @param $tableField
     *
     * @return string
     */
    public function splitTableField($tableField)
    {
        $splitTableField = implode(', ', $tableField);

        return $splitTableField;
    }

    /**
     * This method returns a string formed fron an array, It format the array.
     *
     * @param $tableValues
     *
     * @return string
     */
    public function formatTableValues($tableValues)
    {
        $formattedValues = [];

        foreach ($tableValues as $key => $value) {
            $formattedValues[] = "'".$value."'";
        }

        $ValueSql = implode(',', $formattedValues);

        return $ValueSql;
    }

    /**
     * This method returns a string formed from an array.
     *
     * @param $array
     *
     * @return string
     */
    public function updateArraySql($array)
    {
        $updatedValues = [];

        foreach ($array as $key => $val) {
            $updatedValues[] = "`$key` = '$val'";
        }

        $valueSql = implode(',', $updatedValues);

        return $valueSql;
    }

    /**
     * This method returns column fields of a particular table.
     *
     * @param $table
     *
     * @return array
     */
    public function getColumnNames($table)
    {
        $tableFields = [];

        $sql = 'SHOW COLUMNS FROM '.$table;
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue(':table', $table, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $result) {
            array_push($tableFields, $result['Field']);
        }

        return $tableFields;
    }
}
