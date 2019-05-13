<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class TagsTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> name string(255) mandatory
 * <li> color string(6) mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class TagsTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_tags';
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
				'title' => Loc::getMessage('TAGS_ENTITY_ID_FIELD'),
			),
			'name' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('TAGS_ENTITY_NAME_FIELD'),
			),
			'color' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateColor'),
				'title' => Loc::getMessage('TAGS_ENTITY_COLOR_FIELD'),
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