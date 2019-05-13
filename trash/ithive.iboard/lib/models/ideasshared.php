<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class IdeasSharedTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> idea_id int mandatory
 * <li> user_id int mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class IdeasSharedTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_ideas_shared';
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
				'title' => Loc::getMessage('IDEAS_SHARED_ENTITY_ID_FIELD'),
			),
			'idea_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_SHARED_ENTITY_IDEA_ID_FIELD'),
			),
			'user_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_SHARED_ENTITY_USER_ID_FIELD'),
			),
		);
	}
}