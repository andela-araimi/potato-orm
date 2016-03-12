<?php

/**
 * Class DataBaseModel:
 * This is an abstract class which stands as a model
 * to another class e.g User class which can inherit from
 * all its methods. This class stands as a middle man
 * between the User class and the DataBaseQuery class.
 *
 * @author: Raimi Ademola <ademola.raimi@andela.com>
 * @copyright: 2016 Andela
 */
namespace Demo;

use Doctrine\Common\Inflector\Inflector;

class DataBaseModel implements DataBaseModelInterface
{
    protected $tableName;
    protected $dataBaseConnection;
    protected $DataBaseQuery;
    protected $properties;
    protected $arrayField = [];

    /**
     * This is a constructor; a default method  that will be called automatically during class instantiation.
     */
    public function __construct()
    {
        $this->tableName = self::getClassName();
        $this->DataBaseQuery = new DataBaseQuery(new DataBaseConnection());
        $this->arrayField['id'] = 0;
    }

    /**
     * The magic setter method.
     *
     * @param $properties
     * @param $values
     *
     * @return array associative array properties
     */
    public function __set($properties, $values)
    {
        $this->arrayField[$properties] = $values;
    }

    /**
     * The magic getter method.
     *
     * @param $properties
     *
     * @return array key
     */
    public function __get($properties)
    {
        return $this->arrayField[$properties];
    }

    /**
     * This method gets all the record from a particular table
     * by accessing the read method from the DataBaseQuery class.
     *
     * @throws NoDataFoundException
     *
     * @return associative array
     */
    public static function getAll()
    {
        $sqlData = DataBaseQuery::read($id = false, self::getClassName());

        if (count($sqlData) > 0) {
            return $sqlData;
        }

        throw new NoDataFoundException('There is no data to display');
    }

    /**
     * This method either create or update record in a database table
     * by calling either the read method or create method in the
     * DataBaseQuery class.
     *
     * @throws NoRecordUpdateException
     * @throws EmptyArrayException
     * @throws NoRecordCreatedException
     *
     * @return bool true or false;
     */
    public function save()
    {
        if ($this->arrayField['id']) {
            $sqlData = $this->DataBaseQuery::read($this->arrayField['id'], self::getClassName());
            if ($this->checkIfRecordIsEmpty($sqlData)) {
                $boolCommit = $this->DataBaseQuery->update(['id' => $this->arrayField['id']], $this->arrayField, self::getClassName());
                if ($boolCommit) {
                    return true;
                }
                throw new NoRecordUpdatedException('oops, your record did not update succesfully');
            }
            throw new EmptyArrayException("data passed didn't match any record");
        }
        $sqlData = $this->DataBaseQuery::read($this->arrayField['id'], self::getClassName());

        $boolCommit = $this->DataBaseQuery->create($this->arrayField, self::getClassName());
        if ($boolCommit) {
            return true;
        }
        throw new NoRecordCreatedException('oops,your record did not create succesfully');
    }

    /**
     * This method find a record by id.
     *
     * @param $id
     *
     * @throws ArgumentNumberIncorrectException
     * @throws ArgumentNotFoundException
     *
     * @return object
     */
    public static function findById($id)
    {
        $numArgs = func_num_args();
        if ($numArgs < 0 || $numArgs > 1) {
            throw new ArgumentNumberIncorrectException('Please input just one Argument');
        }
        if ($numArgs == '') {
            throw new ArgumentNotFoundException('No Argument found, please input an argument');
        }

        $staticFindInstance = new static();
        $staticFindInstance->id = $id == '' ? false : $id;

        return $staticFindInstance;
    }

    /**
     * This method find a record by id and returns
     * all the data present in the id.
     *
     *
     * @return associative array
     */
    public function getById()
    {
        if ($this->arrayField['id']) {
            $sqlData = $this->DataBaseQuery::read($this->arrayField['id'], self::getClassName());

            return $sqlData;
        }
    }

    /**
     * This method delete a row from the table by the row id.
     *
     * @param $id
     *
     * @throws ArgumentNumberIncorrectException;
     * @throws ArgumentNotFoundException;
     *
     * @return bool true
     */
    public static function destroy($id)
    {
        $numArgs = func_num_args();
        if ($numArgs < 0 || $numArgs > 1) {
            throw new ArgumentNumberIncorrectException('Please input just one Argument');
        }
        if ($numArgs == ' ') {
            throw new ArgumentNotFoundException('No Argument found, please input an argument');
        }
        $sqlData = DataBaseQuery::read($id, self::getClassName());
        $sqlData = DataBaseQuery::delete($id, self::getClassName());

        return true;

        throw new IdNotFoundException('Oops, the id '.$id.' is not in the database, try another id');
    }

    public static function getClassName()
    {
        $tableName = explode('\\', get_called_class());

        return Inflector::pluralize(strtolower(end($tableName)));
    }

    /**
     * This method check if the argument passed to this function is an array.
     *
     * @param $arrayOfRecord
     *
     * @return bool true
     */
    public function checkIfRecordIsEmpty($arrayOfRecord)
    {
        if (count($arrayOfRecord) > 0) {
            return true;
        }

        return false;
    }

    /**
     * This method check if the argument passed to this function is an array.
     *
     * @param $arrayOfRecord
     *
     * @return bool true
     */
    public function checkIfRecordExist($arrayOfRecord)
    {
        $stringValue = [];
        foreach ($arrayOfRecord as $key => $val) {
            $stringValue[] = $val;
        }
    }
}
