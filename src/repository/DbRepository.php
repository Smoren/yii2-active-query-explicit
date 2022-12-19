<?php

namespace Smoren\Yii2\ActiveRecordExplicit\repository;

use Smoren\Yii2\ActiveRecordExplicit\exceptions\DbConnectionManagerException;
use Smoren\Yii2\ActiveRecordExplicit\exceptions\DbException;
use Smoren\Yii2\ActiveRecordExplicit\interfaces\DbConnectionManagerInterface;
use Smoren\Yii2\ActiveRecordExplicit\interfaces\DbRepositoryInterface;
use Smoren\Yii2\ActiveRecordExplicit\models\ActiveQuery;
use Smoren\Yii2\ActiveRecordExplicit\models\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\NotInstantiableException;
use Yii;

abstract class DbRepository implements DbRepositoryInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param callable|null $filter
     * @return array|ActiveRecord[]
     * @throws DbConnectionManagerException
     */
    public function findAll(?callable $filter = null): array
    {
        try {
            $this->activate();
            return $this->find($filter)->all();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @param callable|null $filter
     * @return array|ActiveRecord
     * @throws DbConnectionManagerException
     * @throws DbException
     */
    public function findOne(?callable $filter = null)
    {
        try {
            $this->activate();
            return $this->find($filter)->one();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @param callable|null $filter
     * @return ActiveRecord|array
     * @throws DbConnectionManagerException
     * @throws DbException
     */
    public function findFirst(?callable $filter = null)
    {
        try {
            $this->activate();
            return $this->find($filter)->first();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @param callable|null $filter
     * @return ActiveQuery
     */
    protected function find(?callable $filter = null): ActiveQuery
    {
        $query = $this->getModelClass()::find();

        if($filter !== null) {
            $filter($query);
        }

        return $query;
    }

    /**
     * @param ActiveRecord $model
     * @return void
     * @throws DbConnectionManagerException
     * @throws DbException
     */
    protected function saveModel(ActiveRecord $model): void
    {
        try {
            $this->activate();
            $model->save();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @param ActiveRecord $model
     * @return int
     * @throws DbConnectionManagerException
     * @throws DbException
     */
    protected function deleteModel(ActiveRecord $model): int
    {
        try {
            $this->activate();
            return $model->delete();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @param ActiveRecord $model
     * @return void
     * @throws DbConnectionManagerException
     */
    protected function refreshModel(ActiveRecord $model): void
    {
        try {
            $this->activate();
            $model->refresh();
        } finally {
            $this->deactivate();
        }
    }

    /**
     * @return $this
     * @throws DbConnectionManagerException
     */
    protected function activate(): self
    {
        $this->getConnectionManager()->attachRepository($this);
        return $this;
    }

    /**
     * @return $this
     * @throws DbConnectionManagerException
     */
    protected function deactivate(): self
    {
        $this->getConnectionManager()->detachRepository($this);
        return $this;
    }

    /**
     * @return DbConnectionManagerInterface
     * @throws DbConnectionManagerException
     */
    protected function getConnectionManager(): DbConnectionManagerInterface
    {
        try {
            return Yii::$container->get(DbConnectionManagerInterface::class);
        } catch(InvalidConfigException|NotInstantiableException $e) {
            throw new DbConnectionManagerException(
                'cannot instantiate',
                DbConnectionManagerException::CANNOT_INSTANTIATE
            );
        }
    }
}
