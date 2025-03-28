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
class ProductQuery extends \yii\db\ActiveRecord
{

    public $query = [];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
          
            [['query'], 'safe'],
            // [['quantity'], 'integer'],
            // [['title', 'description'], 'string', 'max' => 255],
        ];
    }
}
