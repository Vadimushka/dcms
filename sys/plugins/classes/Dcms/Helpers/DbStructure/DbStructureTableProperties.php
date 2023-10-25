<?php

namespace Dcms\Helpers\DbStructure;


class DbStructureTableProperties
{
    public $Name;
    public $Engine;
    public $Version;
    public $Row_format;
    public $Rows;
    public $Avg_row_length;
    public $Data_length;
    public $Max_data_length;
    public $Index_length;
    public $Data_free;
    public $Auto_increment;
    public $Create_time;
    public $Update_time;
    public $Check_time;
    public $Collation;
    public $Charset;
    public $Checksum;
    public $Create_options;
    public $Comment;

    /**
     * @param DbStructureTableColumn[] $columns
     * @param DbStructureTableKey[] $keys
     * @param DbStructureTableForeignKey[] $foreign_keys
     * @return string
     */
    public function getSQLCreate($columns, $keys, $foreign_keys)
    {
        $sql = "CREATE TABLE `{$this->Name}` (" . PHP_EOL;

        $sql_blocks = [];

        foreach ($columns AS $column) {
            $sql_blocks[] = "\t" . $column->getSQLCreate();
        }

        foreach ($keys AS $key) {
            $sql_blocks[] = "\t" . $key->getSQLCreate();
        }

        foreach ($foreign_keys AS $key) {
            $sql_blocks[] = "\t" . $key->getSQLCreate();
        }

        $sql .= join(',' . PHP_EOL, $sql_blocks);

        $sql .= PHP_EOL . ")";
        $sql .= " ENGINE={$this->Engine}";

        if ($this->Auto_increment)
            $sql .= " AUTO_INCREMENT={$this->Auto_increment}";

        $sql .= " DEFAULT CHARSET={$this->Charset}";
        $sql .= " COLLATE={$this->Collation}";
        $sql .= " COMMENT=" . DbStructure::quote($this->Comment);

        return $sql;
    }

    public function fromArray($Properties)
    {
        foreach ($this AS $key => $val) {
            $this->$key = $Properties[$key];
        }
    }
}
