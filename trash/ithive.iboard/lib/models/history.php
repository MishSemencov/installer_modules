<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class HistoryTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> date datetime mandatory
 * <li> user_id int mandatory
 * <li> idea_id int mandatory
 * <li> board_id int mandatory
 * <li> data string mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class HistoryTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_history';
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
				'title' => Loc::getMessage('HISTORY_ENTITY_ID_FIELD'),
			),
			'date' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('HISTORY_ENTITY_DATE_FIELD'),
			),
			'user_id' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('HISTORY_ENTITY_USER_ID_FIELD'),
			),
			'idea_id' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('HISTORY_ENTITY_IDEA_ID_FIELD'),
			),
			'board_id' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('HISTORY_ENTITY_BOARD_ID_FIELD'),
			),
			'data' => array(
				'data_type' => 'text',
				'required' => true,
				'title' => Loc::getMessage('HISTORY_ENTITY_DATA_FIELD'),
			),
            'type' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('HISTORY_ENTITY_TYPE_FIELD'),
            ),
		);
	}
}