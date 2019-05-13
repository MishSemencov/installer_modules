<?php
/**
 * Api data format User class.
 *
 * @package ITHive\API\TakeHeli\V1\Data
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API\Example\V1\Data;

use ITHive\API\Data;
use ITHive\API\Error;

/**
 * Class User
 * @package ITHive\API\TakeHeli\V1\Data
 */
class User extends Data
{
    /** @var int Идентификатор */
    public $id;
    /** @var null|string Имя */
    public $firstName = null;
    /** @var null|string Фамилия */
    public $lastName = null;
    /** @var null|string Электронный адрес */
    public $email = null;
    /** @var string Номер телефона */
    public $phone;

    /** @var array Обязательные поля */
    protected $requiredFields = array(
        'phone'
    );

    /**
     * User constructor.
     * @param null $arData Array of data to construct heli
     * @param bool $mock Create test object?
     * @throws Error
     */
    public function __construct($arData = null, $mock = false)
    {
        if ($mock) {
            $this->setMock($arData);
        } else {
            $this->validate($arData);

            $this->id = $arData['id'];
            $this->firstName = $arData['firstName'];
            $this->lastName = $arData['lastName'];
            $this->email = $arData['email'];
            $this->phone = $arData['phone'];
        }
    }

    /**
     * Construct object and complete empty fields with test data
     * @param $arData
     */
    public function setMock($arData)
    {
        $this->id = $arData['id'] ?: 1;
        $this->firstName = $arData['firstName'] ?: $this->firstName;
        $this->lastName = $arData['lastName'] ?: $this->lastName;
        $this->email = $arData['email'] ?: $this->email;
        $this->phone = $arData['phone'] ?: '+70004488771';
    }
}