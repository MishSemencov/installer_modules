<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class IdeasTagsTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> idea_id int mandatory
 * <li> tag_id int mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class IdeasTagsTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_ideas_tags';
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
				'title' => Loc::getMessage('IDEAS_TAGS_ENTITY_ID_FIELD'),
			),
			'idea_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_TAGS_ENTITY_IDEA_ID_FIELD'),
			),
			'tag_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IDEAS_TAGS_ENTITY_TAG_ID_FIELD'),
			),
		);
	}
}