<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 * @property int|null $sub_catagory
 *
 * @property Category[] $categories
 * @property ProductCategory[] $productCategories
 * @property Category $subCatagory
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['sub_catagory'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['sub_catagory'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['sub_catagory' => 'id']],
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
            'sub_catagory' => 'Sub Catagory',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['sub_catagory' => 'id']);
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategory::class, ['category_id' => 'id']);
    }

    /**
     * Gets query for [[SubCatagory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubCatagory()
    {
        return $this->hasOne(Category::class, ['id' => 'sub_catagory']);
    }
}
