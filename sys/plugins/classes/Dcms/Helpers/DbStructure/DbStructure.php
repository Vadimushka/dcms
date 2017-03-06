<?php

namespace Dcms\Helpers\DbStructure;

use Dcms\Helpers\FileSystem;

class DbStructure
{

    /**
     * @var DbStructureTable[]
     */
    public $Tables;

    /**
     * @var DbStructureProcedure[]
     */
    public $Procedures;

    /**
     * @var DbStructureFunction[]
     */
    public $Functions;

    public function __construct()
    {
        $this->clean();
    }

    public function clean()
    {
        $this->Tables = [];
        $this->Procedures = [];
        $this->Functions = [];
    }

    public function loadFromBase(\PDO $db)
    {
        $q = $db->query("SHOW TABLE STATUS");
        foreach ($q->fetchAll() AS $table_info) {
            $table_struct = new DbStructureTable();
            $table_struct->loadFromBase($db, $table_info['Name']);
            $this->Tables[] = $table_struct;
        }

        $q = $db->query("SHOW PROCEDURE STATUS");
        foreach ($q->fetchAll() AS $proc_info) {
            $proc_struct = new DbStructureProcedure();
            $proc_struct->loadFromBase($db, $proc_info['Name']);
            $this->Procedures[] = $proc_struct;
        }

        $q = $db->query("SHOW FUNCTION STATUS");
        foreach ($q->fetchAll() AS $fnc_info) {
            $fnc_struct = new DbStructureFunction();
            $fnc_struct->loadFromBase($db, $fnc_info['Name']);
            $this->Functions[] = $fnc_struct;
        }
    }

    /**
     * @param string $abs_path
     * @throws \Exception
     */
    public function loadFromDirectory($abs_path)
    {
        $this->clean();
        $abs_path = realpath($abs_path);
        if (!$abs_path) {
            throw new \Exception(__("Не корректный путь"));
        }

        if (!is_dir($abs_path)) {
            throw new \Exception(__("Путь не указывает на директорию"));
        }

        $table_files = (array)glob($abs_path . '/table-*.json');
        foreach ($table_files AS $table_file) {
            $table_struct = new DbStructureTable();
            $table_struct->loadFromJsonFile($table_file);
            $this->Tables[] = $table_struct;
        }

        $proc_files = (array)glob($abs_path . '/procedure-*.json');
        foreach ($proc_files AS $proc_file) {
            $proc_struct = new DbStructureProcedure();
            $proc_struct->loadFromJsonFile($proc_file);
            $this->Procedures[] = $proc_struct;
        }

        $fnc_files = (array)glob($abs_path . '/function-*.json');
        foreach ($fnc_files AS $fnc_file) {
            $fnc_struct = new DbStructureFunction();
            $fnc_struct->loadFromJsonFile($fnc_file);
            $this->Functions[] = $fnc_struct;
        }
    }

    /**
     * @param string $abs_path
     * @throws \Exception
     */
    public function saveToDirectory($abs_path)
    {
        $abs_path = realpath($abs_path);
        if (!$abs_path) {
            throw new \Exception(__("Не корректный путь"));
        }

        if (!is_dir($abs_path)) {
            throw new \Exception(__("Путь не указывает на директорию"));
        }

        $database_create_sql = "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;" . PHP_EOL . PHP_EOL;

        $files_to_remove = (array)glob($abs_path . '/table-*.*');
        foreach ($files_to_remove AS $file_to_remove) {
            unlink($file_to_remove);
        }

        foreach ($this->Tables AS $table) {
            $table->saveToJsonFile($abs_path . '/table-' . $table->Properties->Name . '.json');
            $table->saveToSqlCreateFile($abs_path . '/table-' . $table->Properties->Name . '.sql');
            $database_create_sql .= $table->getSQLCreate() . ';' . PHP_EOL . PHP_EOL;
        }

        $files_to_remove = (array)glob($abs_path . '/procedure-*.*');
        foreach ($files_to_remove AS $file_to_remove) {
            unlink($file_to_remove);
        }

        foreach ($this->Procedures AS $procedure) {
            $procedure->saveToJsonFile($abs_path . '/procedure-' . $procedure->Name . '.json');
            $procedure->saveToSqlCreateFile($abs_path . '/procedure-' . $procedure->Name . '.sql');
            $database_create_sql .= $procedure->getSQLCreate() . ';' . PHP_EOL . PHP_EOL;
        }

        $files_to_remove = (array)glob($abs_path . '/function-*.*');
        foreach ($files_to_remove AS $file_to_remove) {
            unlink($file_to_remove);
        }

        foreach ($this->Functions AS $function) {
            $function->saveToJsonFile($abs_path . '/function-' . $function->Name . '.json');
            $function->saveToSqlCreateFile($abs_path . '/function-' . $function->Name . '.sql');
            $database_create_sql .= $function->getSQLCreate() . ';' . PHP_EOL . PHP_EOL;
        }

        $database_create_sql .= "/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;";

        FileSystem::saveFileContent($abs_path . '/database.sql', $database_create_sql);
    }

    /**
     * @return string[]
     */
    public function getSqlCreate()
    {
        $sql = [];
        foreach ($this->Tables AS $table) {
            $sql[] = $table->getSQLCreate();
        }

        foreach ($this->Procedures AS $procedure) {
            $sql[] = $procedure->getSQLCreate();
        }

        foreach ($this->Functions AS $function) {
            $sql[] = $function->getSQLCreate();
        }

        return $sql;
    }

    /**
     * @param self $structure
     * @return string[]
     */
    public function getSqlChange($structure)
    {
        $queries = [];

        foreach ($this->Tables AS $table) {
            $exists = false;
            foreach ($structure->Tables AS $table_to) {
                if ($table->Properties->Name === $table_to->Properties->Name) {
                    $exists = $table_to;
                    break;
                }
            }
            if (!$exists) {
                $queries[] = "DROP TABLE `{$table->Properties->Name}`";
            } else {
                if ($sql_change = $table->getSQLChange($exists)) {
                    $queries[] = $sql_change;
                }
            }
        }

        foreach ($structure->Tables AS $table_to) {
            $exists = false;
            foreach ($this->Tables AS $table) {
                if ($table->Properties->Name === $table_to->Properties->Name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $queries[] = $table_to->getSQLCreate();
            }
        }

        foreach ($this->Procedures AS $procedure) {
            $exists = false;
            foreach ($structure->Procedures AS $procedure_to) {
                if ($procedure->Name === $procedure_to->Name) {
                    $exists = $procedure_to;
                    break;
                }
            }

            if (!$exists) {
                $queries[] = "DROP PROCEDURE `{$procedure->Name}`";
            } else {
                if ($exists->getSQLCreate() !== $procedure->getSQLCreate()) {
                    $queries[] = "DROP PROCEDURE `{$procedure->Name}`";
                    $queries[] = $exists->getSQLCreate();
                }
            }
        }

        foreach ($structure->Procedures AS $procedure_to) {
            $exists = false;
            foreach ($this->Procedures AS $procedure) {
                if ($procedure->Name === $procedure_to->Name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $queries[] = $procedure_to->getSQLCreate();
            }
        }

        foreach ($this->Functions AS $function) {
            $exists = false;
            foreach ($structure->Functions AS $function_to) {
                if ($function->Name === $function_to->Name) {
                    $exists = $function_to;
                    break;
                }
            }

            if (!$exists) {
                $queries[] = "DROP FUNCTION `{$function->Name}`";
            } else {
                if ($exists->getSQLCreate() !== $function->getSQLCreate()) {
                    $queries[] = "DROP FUNCTION `{$function->Name}`";
                    $queries[] = $exists->getSQLCreate();
                }
            }
        }

        foreach ($structure->Functions AS $function_to) {
            $exists = false;
            foreach ($this->Functions AS $function) {
                if ($function->Name === $function_to->Name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $queries[] = $function_to->getSQLCreate();
            }
        }

        return $queries;
    }

    /**
     * @param $string
     * @return string
     */
    public static function quote($string)
    {
        // TODO: костыль
        return \DB::me()->quote($string);
    }

    /**
     * @param \PDO $db
     * @return string[]
     */
    static public function getAllTables(\PDO $db){
        $tables = array();
        $q = $db->query("SHOW TABLE STATUS");
        foreach ($q->fetchAll() AS $table_info) {
            $tables[] = $table_info['Name'];
        }
        return $tables;
    }
}
