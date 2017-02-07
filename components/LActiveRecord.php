<?php
class LActiveRecord extends CActiveRecord
{
	public static $list_key_field;	// id;
	public static $list_name_field;	// 'name';
	public static $list_sort_field;	// 'id';
	
	public static function listData($params = [], $sql = null)
//	public static function listData($params = [])
	{
		// array with default values
		$values =[
			'show_all'=>true, 	// + [''=>'All']
			'show_na'=>false, 	// + [''=>'n/a']
			'show_empty'=>false,// + ['']
			'enabled'=>false, 	// scope enabled()
			'realonly'=>false, 	// scope realonly()
			'opened'=>false, 	// scope opened()
		];

		if (!is_array($params))
			throw new CHttpException(404,'listData. Old style function Call. Please Correct');
		
		foreach ($params as $key=>$value)
			$values[$key]=$value;

		// now $def with new parameters.
				
		// это началась работа
		$r = [];
		if ($values['show_empty']) $r += [''];
		if ($values['show_all']) $r += [''=>'All'];
		if ($values['show_na']) $r += [''=>'n/a'];

		$model = static::model();
		
		if ($values['enabled']) $model =  $model->enabled();
		if ($values['realonly']) $model =  $model->realonly();
		if ($values['opened']) $model =  $model->opened();
		
		$findParams = ['order'=>static::$list_sort_field];
		if ($sql) $findParams = array_merge ($findParams, ['condition'=>$sql]);
		
		return $r + CHtml::listData($model->findAll($findParams), static::$list_key_field, static::$list_name_field);;
	}
	
	public function getId()
	{
		return $this;
	}
	
	public function getName()
	{
		return $this;
	}
	
	public static function getNameById($id)
	{
		if ($id === null) return "";
		
		$ar = static::model()->findByPk($id);
		if ($ar == null) return "";
		return $ar->name;
	}
}
?>