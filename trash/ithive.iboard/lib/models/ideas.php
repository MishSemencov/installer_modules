<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class IdeasTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> active int mandatory
 * <li> allow_all int mandatory
 * <li> user_id int mandatory
 * <li> sort int mandatory
 * <li> name string(255) optional
 * <li> description string mandatory
 * <li> category_id int mandatory
 * <li> board_id int mandatory
 * <li> important int mandatory
 * <li> date_create datetime mandatory
 * <li> date_update datetime optional
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class IdeasTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_ideas';
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
				'title' => Loc::getMessage('IDEAS_ENTITY_ID_FIELD'),
			),
            'origin_id' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('IDEAS_ENTITY_ORIGIN_ID_FIELD'),
            ),
            'active' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('IDEAS_ENTITY_ACTIVE_FIELD'),
            ),
            'user_id' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('IDEAS_ENTITY_USER_ID_FIELD'),
            ),
            'sort_list' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('IDEAS_ENTITY_SORT_LIST_FIELD'),
            ),
            'sort_table' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('IDEAS_ENTITY_SORT_TABLE_FIELD'),
            ),
            'min_list' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('IDEAS_ENTITY_MIN_LIST_FIELD'),
            ),
            'min_table' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('IDEAS_ENTITY_MIN_TABLE_FIELD'),
            ),
            'name' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateName'),
                'title' => Loc::getMessage('IDEAS_ENTITY_NAME_FIELD'),
            ),
			'description' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('IDEAS_ENTITY_DESCRIPTION_FIELD'),
			),
			'category_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_ENTITY_CATEGORY_ID_FIELD'),
			),
			'board_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_ENTITY_BOARD_ID_FIELD'),
			),
			'important' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_ENTITY_IMPORTANT_FIELD'),
			),
			'date_create' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_ENTITY_DATE_CREATE_FIELD'),
			),
			'date_update' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('IDEAS_ENTITY_DATE_UPDATE_FIELD'),
			),
            'date_visit' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('IDEAS_ENTITY_DATE_VISIT_FIELD'),
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