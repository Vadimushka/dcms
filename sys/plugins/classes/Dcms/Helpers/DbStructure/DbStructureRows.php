<?php

namespace Dcms\Helpers\DbStructure;


use Dcms\Helpers\FileSystem;
use Dcms\Helpers\Json;

class DbStructureRows
{
    private $_data;

    public function __construct()
    {
    }

    public function loadFromBase(\PDO $db, $table)
    {
        $q = $db->query("SELECT * FROM `{$table}`");
        $this->_data = $q->fetchAll();
    }

    public function getSQLReplace(\PDO $db, $table)
    {
//        $sql = "DELETE FROM `{$table}`;" . PHP_EOL;
        $sql = "/*!40000 ALTER TABLE `{$table}` DISABLE KEYS */;" . PHP_EOL;

        foreach ($this->_data AS $row_data) {
            $keys = array_keys($row_data);
            $keys_quotes = [];
            $values_quotes = [];
            foreach ($keys AS $key) {
                $keys_quotes[] = "`{$key}`";
                $values_quotes[] = is_null($row_data[$key]) ? 'null' : $db->quote($row_data[$key]);
            }
            $joined_keys = join(',', $keys_quotes);
            $joined_values = join(',', $values_quotes);
            $sql .= "INSERT INTO `{$table}` ({$joined_keys}) VALUES ({$joined_values});" . PHP_EOL;
        }

        $sql .= "/*!40000 ALTER TABLE `{$table}` ENABLE KEYS */;" . PHP_EOL;
        return $sql;
    }

    public function loadFromJsonFile($path)
    {
        $json = file_get_contents($path);
        $this->_data = Json::parse($json);
    }

    public function saveToJsonFile($path)
    {
        FileSystem::saveFileContent($path, Json::stringify($this->_data));
    }

    public function saveToSQLReplaceFile($path, \PDO $db, $table)
    {
        FileSystem::saveFileContent($path, $this->getSQLReplace($db, $table));
    }
}
