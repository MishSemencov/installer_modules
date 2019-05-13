<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class BoardsTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> name string(255) mandatory
 * <li> user_id int mandatory
 * <li> system int mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class BoardsTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_boards';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'id' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('BOARDS_ENTITY_ID_FIELD'),
			),
			'name' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('BOARDS_ENTITY_NAME_FIELD'),
			),
			'user_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('BOARDS_ENTITY_USER_ID_FIELD'),
			),
			'system' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('BOARDS_ENTITY_SYSTEM_FIELD'),
			),
		);
	}
	/**
	 * Returns validators for name field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
}