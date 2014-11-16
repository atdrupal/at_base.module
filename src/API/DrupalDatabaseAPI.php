<?php

namespace Drupal\at_base\API;

class DrupalDatabaseAPI
{

    /**
     * @param  string $table
     */
    public function select($table, $alias = NULL, array $options = array())
    {
        return db_select($table, $alias, $options);
    }

    /**
     * @param  string $table
     */
    public function update($table, array $options = array())
    {
        return db_update($table, $options);
    }

    /**
     * @param  string $table
     */
    public function delete($table, array $options = array())
    {
        return db_delete($table, $options);
    }

    /**
     * @param  string $table
     */
    public function insert($table, array $options = array())
    {
        return db_insert($table, $options);
    }

    /**
     * @param  string $table
     */
    public function merge($table, array $options = array())
    {
        return db_merge($table, $options);
    }

    /**
     * @param  string $query
     */
    public function query($query, array $args = array(), array $options = array())
    {
        return db_query($query, $args, $options);
    }

}
