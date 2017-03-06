<?php

namespace Dcms\Helpers\DbStructure;


use Dcms\Helpers\FileSystem;
use Dcms\Helpers\Json;

class DbStructureTable
{
    /**
     * @var DbStructureTableProperties
     */
    public $Properties;

    /**
     * @var DbStructureTableColumn[]
     */
    public $Columns;

    /**
     * @var DbStructureTableKey[]
     */
    public $Keys;

    /**
     * @var DbStructureTableForeignKey[]
     */
    public $ForeignKeys;

    function __construct()
    {
        $this->clean();
    }

    public function clean()
    {
        $this->Columns = [];
        $this->Keys = [];
        $this->Properties = new DbStructureTableProperties();
    }

    public function loadFromJsonFile($path)
    {
        $this->clean();
        $json = file_get_contents($path);
        $sourceObj = Json::parse($json);

        $this->Properties->fromArray($sourceObj['Properties']);

        foreach ($sourceObj['Columns'] AS $column){
            $this->Columns[] = $colObj = new DbStructureTableColumn();
            $colObj->fromArray($column);
        }

        foreach ($sourceObj['Keys'] AS $key){
            $this->Keys[] = $keyObj = new DbStructureTableKey();
            $keyObj->fromArray($key);
        }

        foreach ($sourceObj['ForeignKeys'] AS $key){
            $this->ForeignKeys[] = $keyObj = new DbStructureTableForeignKey();
            $keyObj->fromArray($key);
        }
    }

    /**
     * получение структуры таблицы из подключенной базы
     * @param \PDO $db
     * @param string $table Имя таблицы
     * @throws \Exception
     */
    function loadFromBase(\PDO $db, $table)
    {
        $this->clean();
        // получение полей таблицы
        $q = $db->query("SHOW FULL COLUMNS FROM `$table`");

        if (!$q)
            throw new \Exception(__("Не удалось получить информацию о таблице %s", $table));

        $this->Columns = $q->fetchAll(\PDO::FETCH_CLASS, 'Dcms\Helpers\DbStructure\DbStructureTableColumn');

        // получение ключей таблицы
        $q = $db->query("SHOW KEYS FROM `$table`");
        $this->Keys = $q->fetchAll(\PDO::FETCH_CLASS, 'Dcms\Helpers\DbStructure\DbStructureTableKey');

        // получение свойств таблицы
        $q = $db->query("SHOW TABLE STATUS LIKE '$table'");
        $this->Properties = $q->fetchObject('Dcms\Helpers\DbStructure\DbStructureTableProperties');

        $q = $db->query("SELECT CCSA.character_set_name FROM information_schema.`TABLES` T,
       information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
WHERE CCSA.collation_name = T.table_collation
  AND T.table_name = '{$table}'");

        $this->Properties->Charset = $q->fetchColumn();

        // получение внешних ключей таблицы
        $q = $db->query("SELECT
  DISTINCT(kcu.CONSTRAINT_NAME) AS Name,
   kcu.COLUMN_NAME AS Column_name,
	 kcu.REFERENCED_TABLE_NAME AS Reference_table_name,
	 kcu.REFERENCED_COLUMN_NAME AS Reference_column_name,
  rc.UPDATE_RULE AS Update_rule,
  rc.DELETE_RULE AS Delete_rule
FROM
  INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS rc
  JOIN information_schema.KEY_COLUMN_USAGE as kcu ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
  WHERE rc.TABLE_NAME = '$table'");
        $this->ForeignKeys = $q->fetchAll(\PDO::FETCH_CLASS, 'Dcms\Helpers\DbStructure\DbStructureTableForeignKey');

        $this->_removeRedundantKeys();
        $this->_mergeKeys();
    }


    public function saveToJsonFile($path)
    {
        FileSystem::saveFileContent($path, Json::stringify($this));
    }

    public function saveToSqlCreateFile($path)
    {
        FileSystem::saveFileContent($path, $this->getSQLCreate());
    }

    /**
     * получение SQL запроса на создание таблицы
     * @return string SQL запрос на создание таблицы
     */
    function getSQLCreate()
    {
        return $this->Properties->getSQLCreate($this->Columns, $this->Keys, $this->ForeignKeys);
    }

    /**
     * получение SQL запроса на изменение таблицы
     * @param self $tStruct_obj Структура целевой таблицы
     * @return string SQL запрос для приведения структуры таблицы к $tStruct_obj
     */
    function getSQLChange($tStruct_obj)
    {
        $sql_check_foreign_disable = "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;";
        $sql_check_foreign_enable = "/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;";

        $sql = "";
        /** @var string[] $to_delete */
        $to_delete = [];
        /** @var string[] $to_edit */
        $to_edit = [];
        /** @var string[] $to_add */
        $to_add = [];

        // отсутствующие ключи
        foreach ($this->Keys AS $key) {
            $exists = false;
            foreach ($tStruct_obj->Keys AS $key_to) {
                if ($key_to->Key_name === $key->Key_name) {
                    $exists = $key_to;
                    break;
                }
            }

            if (!$exists) {
                $to_delete[] = $key->getSQLDelete();
            } elseif ($edit_sql = $key->getSQLChange($exists)) {
                $to_edit[] = $edit_sql;
            }
        }

        // отсутствующие колонки
        foreach ($this->Columns AS $column) {
            $exists = false;
            foreach ($tStruct_obj->Columns AS $column_to) {
                if ($column_to->Field === $column->Field) {
                    $exists = $column_to;
                    break;
                }
            }

            if (!$exists) {
                $to_delete[] = $column->getSQLDelete();
            } elseif ($edit_sql = $column->getSQLChange($exists)) {
                $to_edit[] = $edit_sql;
            }
        }

        foreach ($tStruct_obj->Columns AS $column_to) {
            $exists = false;
            foreach ($this->Columns AS $column) {
                if ($column_to->Field === $column->Field) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $to_add[] = "ADD " . $column_to->getSQLCreate();
            }
        }

        foreach ($tStruct_obj->Keys AS $key_to) {
            $exists = false;
            foreach ($this->Keys AS $key) {
                if ($key_to->Key_name === $key->Key_name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $to_add[] = "ADD " . $key_to->getSQLCreate();
            }
        }

        if ($to_add || $to_delete || $to_edit) {
            $sql .= "ALTER TABLE `{$this->Properties->Name}`" . PHP_EOL;
            $structure = [];

            if ($to_delete)
                $structure[] = join("\t," . PHP_EOL, $to_delete);

            if ($to_edit)
                $structure[] = join("\t," . PHP_EOL, $to_edit);

            if ($to_add)
                $structure[] = join("\t," . PHP_EOL, $to_add);

            $sql .= implode("," . PHP_EOL, $structure) . ";" . PHP_EOL . PHP_EOL;
        }

        // отсутствующие ключи
        foreach ($this->ForeignKeys AS $key) {
            $exists = false;
            foreach ($tStruct_obj->ForeignKeys AS $key_to) {
                if ($key_to->Name === $key->Name) {
                    $exists = $key_to;
                    break;
                }
            }

            if (!$exists) {
                $sql .= $sql_check_foreign_disable . PHP_EOL;
                $sql .= "ALTER TABLE `{$this->Properties->Name}`" . PHP_EOL;
                $sql .= $key->getSQLDelete() . ';' . PHP_EOL;
                $sql .= $sql_check_foreign_enable . PHP_EOL . PHP_EOL;
            } elseif ($key->getSQLChange($exists)) {
                $sql .= $sql_check_foreign_disable . PHP_EOL;
                $sql .= "ALTER TABLE `{$this->Properties->Name}` ";
                $sql .= $key->getSQLDelete() . ';' . PHP_EOL;
                $sql .= "ALTER TABLE `{$this->Properties->Name}` ";
                $sql .= "ADD " . $exists->getSQLCreate() . ';' . PHP_EOL;
                $sql .= $sql_check_foreign_enable . PHP_EOL . PHP_EOL;
            }
        }

        foreach ($tStruct_obj->ForeignKeys AS $key_to) {
            $exists = false;
            foreach ($this->ForeignKeys AS $key) {
                if ($key_to->Name === $key->Name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
//                $sql .= $sql_check_foreign_disable . PHP_EOL;
                $sql .= "ALTER TABLE `{$this->Properties->Name}`" . PHP_EOL;
                $sql .= "ADD " . $key_to->getSQLCreate() . ';' . PHP_EOL;
//                $sql .= $sql_check_foreign_enable . PHP_EOL . PHP_EOL;
            }
        }

        return $sql;
    }

    /**
     * Объединение полей, относящихся к одному ключу, в один ключ
     */
    private function _mergeKeys()
    {
        /** @var DbStructureTableKey[] $keys */
        $keys = [];

        foreach ($this->Keys AS $tableKey) {
            $key_name = $tableKey->Key_name;
            if (!array_key_exists($key_name, $keys)) {
                $keys[$key_name] = $tableKey;
            } else {
                $columns = $keys[$key_name]->getColumns();
                $columns[] = $tableKey->Column_name;
                $keys[$key_name]->Columns = $columns;
                $keys[$key_name]->Column_name = null;
            }
        }

        $this->Keys = array_values($keys);
    }

    private function _removeRedundantKeys()
    {
        unset ($this->Properties->Auto_increment);
        unset ($this->Properties->Avg_row_length);
        unset ($this->Properties->Check_time);
        unset ($this->Properties->Checksum);
        unset ($this->Properties->Create_time);
        unset ($this->Properties->Data_free);
        unset ($this->Properties->Rows);
        unset ($this->Properties->Data_length);
        unset ($this->Properties->Index_length);
        unset ($this->Properties->Update_time);
        unset ($this->Properties->Version);

        foreach ($this->Keys AS $key) {
            unset($key->Cardinality);
        }
    }
}
