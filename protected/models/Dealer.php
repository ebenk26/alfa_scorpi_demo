<?php

/**
 * This is the model class for table "dealer".
 *
 * The followings are the available columns in table 'dealer':
 * @property integer $id
 * @property string $nama
 * @property string $dealer_name
 * @property string $alamat
 * @property string $email
 * @property integer $region_id
 * @property string $phone
 * @property string $created_by
 * @property string $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Dealer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dealer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('region_id', 'numerical', 'integerOnly'=>true),
			array('nama, dealer_name, alamat, email, phone, created_by, updated_by', 'length', 'max'=>255),
			array('created_at, updated_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nama, dealer_name, alamat, email, region_id, phone, created_by, updated_by, created_at, updated_at, deleted_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nama' => 'Nama',
			'dealer_name' => 'Dealer Name',
			'alamat' => 'Alamat',
			'email' => 'Email',
			'region_id' => 'Region',
			'phone' => 'Phone',
			'created_by' => 'Created By',
			'updated_by' => 'Updated By',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nama',$this->nama,true);
		$criteria->compare('dealer_name',$this->dealer_name,true);
		$criteria->compare('alamat',$this->alamat,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('region_id',$this->region_id);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('created_by',$this->created_by,true);
		$criteria->compare('updated_by',$this->updated_by,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Dealer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
