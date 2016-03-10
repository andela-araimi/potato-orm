<?php

namespace Demo;

interface DataBaseModelInterface
{
    /**
     * This method gets all the record from a particular table.
     *
     *
     * @return associative array
     */
    public function getAll();

    /**
     * This method create or update record in a database table.
     *
     *
     * @return true or false;
     */
    public function save();

    /**
     * This method find a record by id.
     *
     * @param $id
     *
     * @return object
     */
    public static function findById($id);

    /**
     * This method find a record by id and get the data.
     *
     * @return object find
     */
    public function getById();

    /**
     * This method delete a row from the table by the row id.
     *
     * @param $id
     *
     * @return true
     */
    public function destroy($id);
}
