<?php

class RegionMdl extends Region
{
	public static function getOptions(){

		$criteria = new CDbCriteria();
		//$criteria->compare('is_active', 1);
		$arData = Region::model()->findAll($criteria);

		if (is_array($arData)){
			$data = array();
			foreach ($arData as $key => $value) {
				$data[$value->id] =  strtoupper($value->name);
			}

			return $data;
		}

		return array();
	}

}

?>