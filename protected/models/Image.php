<?php

/**
 * This is the model class for table "image".
 * Note: I have to change this Model to Images instead of Image because of this name is conflict with Image.php in the Extension
 *
 * The followings are the available columns in table 'image':
 * @property integer $id
 * @property string $tag
 * @property string $url
 * @property string $license
 * @property string $photographer
 * @property string $source
 *
 * The followings are the available model relations:
 * @property Dataset[] $datasets
 */
class Image extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Image the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('license, photographer, source', 'required'),
            array('tag', 'length', 'max'=>120),
            array('url, source', 'length', 'max'=>256),
            array('photographer', 'length', 'max'=>128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, tag, url, license, photographer, source', 'safe', 'on'=>'search'),
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
            'datasets' => array(self::HAS_MANY, 'Dataset', 'image_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'tag' => 'Image Tag',
            'url' => 'Image URL',
            'license' => 'Image License',
            'photographer' => 'Image Photographer',
            'source' => 'Image Source',
            'image_upload' => 'Upload Image',
        );
    }
}