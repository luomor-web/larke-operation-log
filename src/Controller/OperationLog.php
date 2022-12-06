<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog\Controller;

use Illuminate\Http\Request;

use Larke\Admin\Annotation\RouteRule;
use Larke\Admin\Http\Controller as BaseController;

use Larke\Admin\OperationLog\Model\OperationLog as OperationLogModel;

/**
 * 操作日志
 *
 * @create 2022-2-15
 * @author deatil
 */
#[RouteRule(
    title: "操作日志", 
    desc:  "操作日志管理",
    order: 550,
    auth:  true,
    slug:  "larke-admin.ext.operation-log"
)]
class OperationLog extends BaseController
{
    /**
     * 列表
     *
     * @param  Request  $request
     * @return Response
     */
    #[RouteRule(
        title:  "日志列表", 
        desc:   "操作日志全部列表",
        order:  551,
        parent: "larke-admin.ext.operation-log",
        auth:   true
    )]
    public function index(Request $request)
    {
        $start = (int) $request->input('start', 0);
        $limit = (int) $request->input('limit', 10);
        
        $order = $this->formatOrderBy($request->input('order', 'create_time__ASC'));
        
        $searchword = $request->input('searchword', '');
        $orWheres = [];
        if (! empty($searchword)) {
            $orWheres = [
                ['admin_name', 'like', '%'.$searchword.'%'],
                ['url', 'like', '%'.$searchword.'%'],
                ['method', 'like', '%'.$searchword.'%'],
            ];
        }

        $wheres = [];
        
        $startTime = $this->formatDate($request->input('start_time'));
        if ($startTime !== false) {
            $wheres[] = ['create_time', '>=', $startTime];
        }
        
        $endTime = $this->formatDate($request->input('end_time'));
        if ($endTime !== false) {
            $wheres[] = ['create_time', '<=', $endTime];
        }
        
        $status = $this->switchStatus($request->input('status'));
        if ($status !== false) {
            $wheres[] = ['status', $status];
        }
        
        $method = $request->input('method');
        if (!empty($method)) {
            $wheres[] = ['method', $method];
        }
        
        $query = OperationLogModel::orWheres($orWheres)
            ->wheres($wheres);
        
        $total = $query->count(); 
        $list = $query
            ->offset($start)
            ->limit($limit)
            ->withCertain('admin', ['name', 'nickname', 'email', 'avatar', 'last_active', 'last_ip'])
            ->orderBy($order[0], $order[1])
            ->get()
            ->toArray(); 
        
        return $this->success(__('获取成功'), [
            'start' => $start,
            'limit' => $limit,
            'total' => $total,
            'list' => $list,
        ]);
    }
    
    /**
     * 详情
     *
     * @param string $id
     * @return Response
     */
    #[RouteRule(
        title:  "日志详情", 
        desc:   "操作日志详情",
        order:  552,
        parent: "larke-admin.ext.operation-log",
        auth:   true
    )]
    public function detail(string $id)
    {
        if (empty($id)) {
            return $this->error(__('日志ID不能为空'));
        }
        
        $info = OperationLogModel::where(['id' => $id])
            ->withCertain('admin', ['name', 'email', 'avatar', 'last_active', 'last_ip'])
            ->first();
        if (empty($info)) {
            return $this->error(__('日志信息不存在'));
        }
        
        return $this->success(__('获取成功'), $info);
    }
    
    /**
     * 删除
     *
     * @param string $id
     * @return Response
     */
    #[RouteRule(
        title:  "日志删除", 
        desc:   "操作日志删除",
        order:  553,
        parent: "larke-admin.ext.operation-log",
        auth:   true
    )]
    public function delete(string $id)
    {
        if (empty($id)) {
            return $this->error(__('日志ID不能为空'));
        }
        
        $info = OperationLogModel::where('id', $id)
            ->first();
        if (empty($info)) {
            return $this->error(__('日志信息不存在'));
        }
        
        $deleteStatus = $info->delete();
        if ($deleteStatus === false) {
            return $this->error(__('日志删除失败'));
        }
        
        return $this->success(__('日志删除成功'));
    }
    
    /**
     * 清空一个月前的操作日志|清空特定ID日志
     *
     * @param  Request  $request
     * @return Response
     */
    #[RouteRule(
        title:  "清空日志", 
        desc:   "清空操作日志",
        order:  554,
        parent: "larke-admin.ext.operation-log",
        auth:   true
    )]
    public function clear(Request $request)
    {
        $ids = $request->input('ids');
        if (! empty($ids)) {
            $ids = explode(',', $ids);
            $status = OperationLogModel::whereIn('id', $ids)->delete();
        } else {
            $status = OperationLogModel::where('create_time', '<=', time() - (86400 * 30))
                ->delete();
        }
        
        if ($status === false) {
            return $this->error(__('日志批量删除失败'));
        }
        
        return $this->success(__('日志批量删除成功'));
    }
    
}