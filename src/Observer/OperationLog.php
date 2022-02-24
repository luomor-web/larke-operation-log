<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog\Observer;

use Larke\Admin\OperationLog\Model\OperationLog as OperationLogModel;

class OperationLog
{
    /**
     * 获取模型实例后
     */
    public function retrieved(OperationLogModel $model)
    {
    }

    /**
     * 插入到数据库前
     */
    public function creating(OperationLogModel $model)
    {
        $model->id = md5(mt_rand(100000, 999999).microtime().uniqid());
        
        $model->create_time = time();
        $model->create_ip = request()->ip();
    }

    /**
     * 插入到数据库后
     */
    public function created(OperationLogModel $model)
    {
    }

    /**
     * 更新到数据库
     */
    public function updating(OperationLogModel $model)
    {
    }

    /**
     * 更新到数据库
     */
    public function updated(OperationLogModel $model)
    {
    }

    /**
     * 保存到数据库
     */
    public function saving(OperationLogModel $model)
    {
    }

    /**
     * 保存到数据库
     */
    public function saved(OperationLogModel $model)
    {
    }

    /**
     * 删除
     */
    public function deleting(OperationLogModel $model)
    {
    }

    /**
     * 删除
     */
    public function deleted(OperationLogModel $model)
    {
    }

    /**
     * 恢复软删除前
     */
    public function restoring(OperationLogModel $model)
    {
    }
    
    /**
     * 恢复软删除后
     */
    public function restored(OperationLogModel $model)
    {
    }
}
