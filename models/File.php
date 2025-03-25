<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $title
 * @property int $prodcut_id
 *
 * @property Product $prodcut
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'prodcut_id'], 'required'],
            [['prodcut_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['prodcut_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['prodcut_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'prodcut_id' => 'Prodcut ID',
        ];
    }

    /**
     * Gets query for [[Prodcut]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdcut()
    {
        return $this->hasOne(Product::class, ['id' => 'prodcut_id']);
    }
}
