<?php

namespace backend\modules\ezforms2\models;

use Yii;
/**
 * This is the model class for table "tbdata_1435745159010048800".
 *
 * @property integer $id
 */
class TbdataAll extends \yii\db\ActiveRecord
{
    protected static $table;
    
    public function attributes()
    {
	$attrDB = array_keys(static::getTableSchema()->columns);
	$colFieldsID=[];
	
        return array_merge($attrDB, $colFieldsID);
    }
    
    public function rules() {
	$safe = array_keys(static::getTableSchema()->columns);
	
	return [
	    [$safe, 'safe']
	];
    }
    
    public static function tableName()
    {
        return self::$table;
    }

    /* UPDATE */
    public static function setTableName($table)
    {
        self::$table = $table;
    }

}
