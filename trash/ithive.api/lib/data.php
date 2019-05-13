<?php
/**
 * Data abstract class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */

namespace ITHive\API;

/**
 * Abstract data. Share methods for all data.
 * @package ITHive\API
 */
abstract class Data
{
    /** @var array Required fields*/
    protected $requiredFields = array();

    /**
     * Validate data
     *
     * @param $arData
     * @return Error
     * @throws \ITHive\API\Error
     */
    protected function validate(&$arData){
        $entityName = strtolower(substr(strrchr(get_called_class(), "\\"), 1));

        // check required fields
        $arParams = array_keys($arData);
        foreach ($this->requiredFields as $field) {
            if (!in_array($field, $arParams)) throw new Error('Absent required field: ' . $field . ' in '. $entityName, Error::API_ERR_FIELD_REQUIRED);
        }
    }
}
