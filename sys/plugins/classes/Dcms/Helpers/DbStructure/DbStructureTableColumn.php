<?php

namespace Dcms\Helpers\DbStructure;


class DbStructureTableColumn implements DbStructureTablePartI
{
    public $Field;
    public $Type;
    public $Collation;
    public $Charset;
    public $Null;
    public $Key;
    public $Default;
    public $Extra;
    public $Privileges;
    public $Comment;

    /**
     * @return string
     */
    public function getSQLCreate()
    {
        $sql = "`{$this->Field}` {$this->Type}";

        if ($this->Charset) {
            $sql .= " CHARACTER SET $this->Charset";
        }
        if ($this->Collation) {
            $sql .= " COLLATE $this->Collation";
        }

        $sql .= ($this->Null == "NO" ? ' NOT' : '') . ' NULL';

        if (!is_null($this->Default) || $this->Null == "NO") {
            if (is_null($this->Default)) {
                if ($this->Null !== "NO") {
                    $sql .= ' DEFAULT NULL';
                }
            } else if ($this->Default === 'CURRENT_TIMESTAMP')
                $sql .= ' DEFAULT CURRENT_TIMESTAMP';
            else
                $sql .= " DEFAULT " . DbStructure::quote($this->Default);
        }

        if ($this->Extra)
            $sql .= " " . $this->Extra;

        if ($this->Comment)
            $sql .= " COMMENT " . DbStructure::quote($this->Comment);

        return $sql;
    }

    /**
     * @return string
     */
    public function getSQLDelete()
    {
        return "DROP `{$this->Field}`";
    }

    /**
     * @param self $struct
     * @return bool|string
     */
    public function getSQLChange($struct)
    {
        $compare_column_props = ['Field', 'Type', 'Null', 'Default', 'Extra', 'Comment'];

        $different = false;
        foreach ($compare_column_props AS $compare_key) {
            if ($this->$compare_key !== $struct->$compare_key) {
                $different = true;
                break;
            }
        }

        if (!$different)
            return false;

        return "CHANGE `{$this->Field}` " . $struct->getSQLCreate();
    }

    public function fromArray($column)
    {
        foreach ($this AS $key => $val) {
            $this->$key = $column[$key];
        }
    }
}
