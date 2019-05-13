<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class CategoriesTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> name string(255) mandatory
 * <li> color string(6) mandatory
 * <li> board_id int mandatory
 * <li> user_id int mandatory
 * <li> system int mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class CategoriesTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_categories';
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
				'title' => Loc::getMessage('CATEGORIES_ENTITY_ID_FIELD'),
			),
			'name' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('CATEGORIES_ENTITY_NAME_FIELD'),
			),
			'color' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateColor'),
				'title' => Loc::getMessage('CATEGORIES_ENTITY_COLOR_FIELD'),
			),
            'sort' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('CATEGORIES_ENTITY_SORT_FIELD'),
            ),
			'board_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('CATEGORIES_ENTITY_BOARD_ID_FIELD'),
			),
			'user_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('CATEGORIES_ENTITY_USER_ID_FIELD'),
			),
			'system' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('CATEGORIES_ENTITY_SYSTEM_FIELD'),
			),
            'incoming' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('CATEGORIES_ENTITY_INCOMING_FIELD'),
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
	/**
	 * Returns validators for color field.
	 *
	 * @return array
	 */
	public static function validateColor()
	{
		return array(
			new Main\Entity\Validator\Length(null, 6),
		);
	}
}