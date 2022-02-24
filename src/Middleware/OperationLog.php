<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog\Middleware;

use Closure;

use Larke\Admin\OperationLog\Model\OperationLog as OperationLogModel;

/**
 * 日志
 *
 * @create 2022-2-23
 * @author deatil
 */
class OperationLog
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    
    /**
     * 在响应发送到浏览器后处理任务。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        // 账号信息
        $adminInfo = app('larke-admin.auth-admin')->getProfile();
        $input = $request->except([
            'password', 
            'oldpassword', 
            'newpassword', 
            'newpassword_confirm', 
        ]);
        
        // 数据库检测
        try {
            // 记录日志
            OperationLogModel::record([
                'admin_id' => $adminInfo['id'] ?? 0,
                'admin_name' => $adminInfo['name'] ?? '-',
                'info' => json_encode($input, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
                'status' => 1,
            ]);
        } catch(\Exception $e) {}
    }
}
