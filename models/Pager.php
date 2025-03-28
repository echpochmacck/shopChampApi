<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property float $price
 * @property int $quantity
 *
 * @property CartComposition[] $cartCompositions
 * @property File[] $files
 * @property OrderComposition[] $orderCompositions
 * @property ProductCategory[] $productCategories
 */
class Pager extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'order';
    }

    public $newFiles = [];
    public $page = '';
    public $count = '';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // ['files', 'file', 'extensions' => 'png, jpeg, jpg', 'maxFiles' => 3],
            // [['title', 'description', 'price', 'quantity'], 'required'],
            [['page', 'count'], 'required'],
            [['page', 'count'], 'integer', 'min' => 1],
            // [['quantity'], 'integer'],
            // [['title', 'description'], 'string', 'max' => 255],
        ];
    }
}
