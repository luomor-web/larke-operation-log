<?php

return [
    'title' => '操作日志',
    'url' => '#',
    'method' => 'OPTIONS',
    'slug' => $slug,
    'description' => '记录 admin 系统的相关操作日志',
    'children' => [
        [
            'title' => '日志列表',
            'url' => 'operation-log',
            'method' => 'GET',
            'slug' => 'larke-admin.operation-log.index',
            'description' => '日志列表',
        ],
        [
            'title' => '日志详情',
            'url' => 'operation-log/{id}',
            'method' => 'GET',
            'slug' => 'larke-admin.operation-log.detail',
            'description' => '日志详情',
        ],
        
        [
            'title' => '清空日志',
            'url' => 'operation-log/clear',
            'method' => 'DELETE',
            'slug' => 'larke-admin.operation-log.clear',
            'description' => '清空日志',
        ],
        [
            'title' => '删除日志',
            'url' => 'operation-log/{id}',
            'method' => 'DELETE',
            'slug' => 'larke-admin.operation-log.delete',
            'description' => '删除某条日志',
        ],
    ],
];
