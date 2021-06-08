<?php

namespace Smoren\Yii2\ActiveRecordExplicit\wrappers;


use Smoren\ExtendedExceptions\LogicException;
use Smoren\Yii2\ActiveRecordExplicit\models\ActiveRecord;
use Smoren\Yii2\ActiveRecordExplicit\exceptions\DbException;
use Throwable;
use yii\db\StaleObjectException;

/**
 * Абстрактный класс для обёртывания ActiveRecord модели
 */
abstract class ActiveRecordWrapper extends BaseWrapper
{
    /** @var ActiveRecord */
    protected $item;

    /**
     * Показывает, новый ли объект
     * @return bool
     */
    public function isNewRecord(): bool
    {
        return $this->item->isNewRecord;
    }
}