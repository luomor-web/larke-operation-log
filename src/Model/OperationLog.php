<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog\Model;

use Larke\Admin\Model\Base;
use Larke\Admin\Model\Admin;

/*
 * 登陆日志
 *
 * @create 2022-2-23
 * @author deatil
 */
class OperationLog extends Base
{
    protected $table = 'ext_operation_log';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    
    protected $guarded = [];
    
    public $incrementing = false;
    public $timestamps = false;
    
    public function setUrlAttribute($value) 
    {
        $this->attributes['url'] = $value;
    }
    
    /**
     * 日志用户
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }
    
    /**
     * 记录日志
     */
    public static function record($data = [])
    {
        $data = array_merge([
            'method' => app()->request->method(),
            'url' => urldecode(request()->getUri()),
            'ip' => request()->ip(),
            'useragent' => request()->server('HTTP_USER_AGENT'),
        ], $data);
        
        self::create($data);
    }

}