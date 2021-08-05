<?php

namespace Overfish\Beiwo\Db;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;
use Overtrue\Beiwo\Foundation\Exceptions\NotFoundException;

/**
 * AR基类
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class
            ],
        ];

        // soft delete
        if (static::getTableSchema()->getColumn('deleted_at')) {
            $behaviors['softDelete'] = [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => function ($model) {
                        return time();
                    }
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => 0,
                ],
                'replaceRegularDelete' => true // mutate native `delete()` method
            ];
        }

        // record operator
        if (static::getTableSchema()->getColumn('created_by')) {
            $behaviors['blameable'] = [
                'class' => BlameableBehavior::class
            ];
        }

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     * @return \yii\db\ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        $query = parent::find();
        if (static::getTableSchema()->getColumn('deleted_at')) {
            $query->attachBehavior('softDelete', SoftDeleteQueryBehavior::className());
        }

        return $query;
    }

    /**
     * find or throw exception
     * @param $condition
     * @return null|static
     * @throws NotFoundException
     */
    public static function findOrFail($condition)
    {
        if (($model = static::findOne($condition)) !== null) {
            return $model;
        }

        throw new NotFoundException('the model cannot be found');
    }
}
