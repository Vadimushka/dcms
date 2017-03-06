<?php

namespace Dcms\Helpers\DbStructure;


use Dcms\Helpers\FileSystem;
use Dcms\Helpers\Json;
use JsonMapper;

class DbStructureProcedure
{
    public $Name;
    public $Create;

    public function clean()
    {
        $this->Name = null;
        $this->Create = null;
    }

    public function loadFromJsonFile($path)
    {
        $this->clean();
        $json = file_get_contents($path);
        $mapper = new JsonMapper();
        $data = Json::parse($json, false);
        $mapper->map($data, $this);
    }

    public function loadFromBase(\PDO $db, $Name)
    {
        $this->clean();
        $q = $db->query("SHOW CREATE PROCEDURE `{$Name}`");
        $info = $q->fetch();
        $this->Name = $info['Procedure'];
        $this->Create = $info['Create Procedure'];
        $this->_replaceDefiner();
    }

    private function _replaceDefiner()
    {
        $this->Create = preg_replace('/DEFINER=`[^`]+`@`[^`]+`/', 'DEFINER=CURRENT_USER', $this->Create);
    }

    public function saveToJsonFile($path)
    {
        FileSystem::saveFileContent($path, Json::stringify($this));
    }

    public function saveToSqlCreateFile($path)
    {
        FileSystem::saveFileContent($path, $this->getSQLCreate());
    }

    function getSQLCreate()
    {
        return $this->Create;
    }
}
