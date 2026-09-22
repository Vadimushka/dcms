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

        if ($this->allowsDefault() && (!is_null($this->Default) || $this->Null == "NO")) {
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
            // значение по умолчанию у таких типов невозможно, и в эталоне оно
            // встречается по недосмотру: сравнивать его не с чем
            if ($compare_key === 'Default' && !$this->allowsDefault() && !$struct->allowsDefault())
                continue;

            if (!DbStructure::sameValue($this->$compare_key, $struct->$compare_key)) {
                $different = true;
                break;
            }
        }

        if (!$different)
            return false;

        return "CHANGE `{$this->Field}` " . $struct->getSQLCreate();
    }

    /**
     * MySQL не разрешает DEFAULT у BLOB, TEXT, JSON и GEOMETRY: запрос с ним
     * падает с ошибкой 1101. Такая колонка в описании таблицы осталась от
     * старых версий движка, где структуру снимали с базы без проверки.
     *
     * @return bool
     */
    public function allowsDefault()
    {
        return !preg_match('/^\s*(tiny|medium|long)?(blob|text)\b|^\s*(json|geometry|point|linestring|polygon|multipoint|multilinestring|multipolygon|geometrycollection)\b/i', (string) $this->Type);
    }

    public function fromArray($column)
    {
        foreach ($this AS $key => $val) {
            // в сохранённой структуре часть сведений отсутствует — это
            // состояние конкретной таблицы, а не её описание
            if (array_key_exists($key, $column))
                $this->$key = $column[$key];
        }
    }
}
