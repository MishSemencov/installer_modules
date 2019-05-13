<?php
namespace ITHive\IBoard\Models;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class RemindersTable
 * 
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> date datetime mandatory
 * <li> type string(255) mandatory
 * <li> idea_id int mandatory
 * </ul>
 *
 * @package ITHive\IBoard\Models
 **/

class RemindersTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ithive_iboard_reminders';
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
                'title' => Loc::getMessage('REMINDERS_ENTITY_ID_FIELD'),
            ),
            'date_create' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('REMINDERS_ENTITY_DATE_CREATE_FIELD'),
            ),
            'date' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('REMINDERS_ENTITY_DATE_FIELD'),
            ),
            'period' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('REMINDERS_ENTITY_PERIOD_FIELD'),
            ),
            'idea_id' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('REMINDERS_ENTITY_IDEA_ID_FIELD'),
            ),
		);
	}
	/**
	 * Returns validators for type field.
	 *
	 * @return array
	 */
	public static function validateType()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
}