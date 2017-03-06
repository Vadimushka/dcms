<?php
/**
 * Created by IntelliJ IDEA.
 * User: pavlovd
 * Date: 07.03.2016
 * Time: 18:35
 */

namespace Dcms\Helpers\DbStructure;


interface DbStructureTablePartI
{
    /**
     * @return string
     */
    public function getSQLCreate();

    /**
     * @return string
     */
    public function getSQLDelete();

    /**
     * @param self $struct
     * @return bool|string
     */
    public function getSQLChange($struct);

    /**
     * @param $array
     */
    public function fromArray($array);
}
