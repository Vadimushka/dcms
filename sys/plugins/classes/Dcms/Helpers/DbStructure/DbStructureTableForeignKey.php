<?php

namespace Dcms\Helpers\DbStructure;


class DbStructureTableForeignKey implements DbStructureTablePartI
{
    public $Name;
    public $Column_name;
    public $Reference_table_name;
    public $Reference_column_name;
    public $Update_rule;
    public $Delete_rule;

    /**
     * @return string
     */
    public function getSQLCreate()
    {
        $sql = "CONSTRAINT `{$this->Name}` FOREIGN KEY (`{$this->Column_name}`) REFERENCES `{$this->Reference_table_name}` (`{$this->Reference_column_name}`)";

        $sql .= " ON DELETE " . $this->Delete_rule;
        $sql .= " ON UPDATE " . $this->Update_rule;

        return $sql;
    }

    /**
     * @return string
     */
    public function getSQLDelete()
    {
        return "DROP FOREIGN KEY `{$this->Name}`";
    }

    /**
     * @param self $struct
     * @return bool|string
     */
    public function getSQLChange($struct)
    {
        $compare_key_props = ['Column_name', 'Reference_table_name', 'Reference_column_name', 'Update_rule', 'Delete_rule'];

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
