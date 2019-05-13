<?php
/**
 * Module validator class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API;

/**
 * Validate different type of api exchange data
 * @package ITHive\API
 */
class Validator
{
    /**
     * Validate required. Might not be null.
     * @param $param
     * @param $name
     * @throws Error
     */
    protected static function checkRequired($param, $name){
        if (is_null($param)) {
            throw new Error('Absent required parameter: ' . $name, Error::API_ERR_PARAM_REQUIRED);
        }
    }

    /**
     * Validate boolean type.
     * @param $param
     * @param $name
     * @param bool $required
     * @throws Error
     */
    public static function checkBoolean($param, $name, $required = false)
    {
        if ($required) {
            static::checkRequired($param, $name);
        }

        if (!is_null($param) && !is_bool($param)) {
            throw new Error('Parameter ' . $name . ' must be a boolean', Error::API_ERR_PARAM_FORMAT_BOOLEAN);
        }
    }

    /** Validate number type
     * @param $param
     * @param $name
     * @param bool $required
     * @throws Error
     */
    public static function checkNumber($param, $name, $required = false){
        if($required){
            static::checkRequired($param, $name);
        }

        if (!is_null($param) && intval($param) <= 0) {
            throw new Error('Parameter '. $name . ' must be a number', Error::API_ERR_PARAM_FORMAT_NUMBER);
        }
    }

    /**
     * Validate custom entity type. This types contain at Data namespace.
     * @param $param
     * @param $name
     * @param $fullClassName
     * @param bool $required
     * @throws Error
     */
    public static function checkEntity($param, $name, $fullClassName, $required = false)
    {
        if ($required) {
            static::checkRequired($param, $name);
        }
        $className = substr(strrchr($fullClassName, "\\"), 1);

        if (!is_null($param) && !is_a($param, $fullClassName)) {
            throw new Error('Parameter ' . $name . ' must be an instance of ' . $className, Error::API_ERR_PARAM_FORMAT_ENTITY);
        }
    }

    /**
     * Validate entity Date type. This type contain at Data\Date
     * @param $param
     * @param $name
     * @param bool $required
     * @throws Error
     */
    public static function checkDate($param, $name, $required = false)
    {
        if ($required) {
            static::checkRequired($param, $name);
        }

        // TODO: check date format

        if (!is_null($param) && !is_a($param, "\\DateTime")) {
            throw new Error('Parameter ' . $name . ' must be a date', Error::API_ERR_PARAM_FORMAT_DATE);
        }
    }

    /**
     * Validate array and array of entities. Types of entities contain at Data namespace.
     * @param $param
     * @param $name
     * @param bool $required
     * @param bool $entity
     * @throws Error
     */
    public static function checkArray($param, $name, $required = false, $entity = false)
    {
        if ($required) {
            static::checkRequired($param, $name);
        }

        if (!is_array($param) && !is_null($param)) {
            throw new Error('Parameter ' . $name . ' must be an array', Error::API_ERR_PARAM_FORMAT_ARRAY);
        }

        if (is_array($param) && count($param) <= 0 && !is_null($param)) {
            throw new Error('Parameter ' . $name . ' is empty. It must contain one or more elements.', Error::API_ERR_PARAM_FORMAT_ARRAY_EMPTY);
        }

        // array of objects
        if($entity){
            foreach ($param as $e) {
                static::checkEntity($e, 'at array ' . $name, $entity, false);
            }

        }
    }
}
