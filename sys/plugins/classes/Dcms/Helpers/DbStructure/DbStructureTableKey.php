<?php

namespace Dcms\Helpers\DbStructure;


class DbStructureTableKey implements DbStructureTablePartI
{
    public $Table;
    public $Non_unique;
    public $Key_name;
    public $Seq_in_index;
    public $Column_name;
    public $Columns;
    public $Collation;
    public $Cardinality;
    public $Sub_part;
    public $Packed;
    public $Null;
    public $Index_type;
    public $Comment;
    public $Index_comment;

    public function getColumns()
    {
        return $this->Columns ? $this->Columns : [$this->Column_name];
    }

    public function getColumnsString()
    {
        $columns = [];
        foreach ($this->getColumns() AS $column_name) {
            $columns[] = "`{$column_name}`";
        }
        return join(',', $columns);
    }

    /**
     * @return string
     */
    public function getSQLCreate()
    {
        $is_primary = $this->Key_name === 'PRIMARY';
        $is_unique = !$this->Non_unique;
        $key = $this->Index_type === "FULLTEXT" ? "FULLTEXT" : "KEY";

        if ($is_primary) {
            $sql = "PRIMARY KEY ({$this->getColumnsString()})";
        } else {
            $sql = ($is_unique ? 'UNIQUE ' : '') . "$key `{$this->Key_name}` ({$this->getColumnsString()})";
        }

        return $sql;
    }

    /**
     * @return string
     */
    public function getSQLDelete()
    {
        $is_primary = $this->Key_name === 'PRIMARY';

        $sql = 'DROP ';

        if ($is_primary) {
            $sql .= "PRIMARY KEY ({$this->getColumnsString()})";
        } else {
            $sql .= "INDEX `{$this->Key_name}`";
        }

        return $sql;
    }

    /**
     * @param self $struct
     * @return bool|string
     */
    public function getSQLChange($struct)
    {
        $compare_key_props = ['Non_unique', 'Key_name', 'Column_name', 'Columns', 'Comment'];

        $different = false;
        foreach ($compare_key_props AS $compare_key) {
            if ($this->$compare_key !== $struct->$compare_key) {
                $different = true;
                break;
            }
        }

        if (!$different)
            return false;

        return $this->getSQLDelete() . ', ' . PHP_EOL . 'ADD ' . $struct->getSQLCreate();
    }

    public function fromArray($keyArr)
    {
        foreach ($this AS $key => $val) {
            $this->$key = $keyArr[$key];
        }
    }
}
