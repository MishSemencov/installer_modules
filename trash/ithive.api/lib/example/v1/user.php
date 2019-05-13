<?php
/**
 * Module User class.
 *
 * @package ITHive\API\TakeHeli\V1
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API\Example\V1;

use ITHive\API\Error;
use ITHive\API\Request;

/**
 * Class User
 * @property int id
 * @property null|string firstName
 * @property null|string lastName
 * @property null|string email
 * @property string phone
 * @package ITHive\API\TakeHeli\V1
 */
class User extends Request implements Interfaces\User
{
    /**
     * Api method
     * Get user profile
     * @api
     * @return array
     * @throws \ITHive\API\Error
     */
	 
	public function getComment()
	{
		$data = [
			'comment' => [
				'title' => '1111',
				'author' => '222',
			]
		];
		return $data;
	}

	public function getProfile()
	{
		return ['111'];
	}

	public function setProfile()
	{
		// TODO: Implement setProfile() method.
	}
}