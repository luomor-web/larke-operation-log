<?php

use Larke\Admin\Facade\Extension;

Extension::routes(function ($router) {
    $router
        ->namespace('Larke\\Admin\\OperationLog\\Controller')
        ->group(function ($router) {
            // 日志
            $router->get('/operation-log', 'OperationLog@index')
                ->name('larke-admin.operation-log.index');
            
            $router->get('/operation-log/{id}', 'OperationLog@detail')
                ->name('larke-admin.operation-log.detail')
                ->where('id', '[A-Za-z0-9\-]+');
            
            $router->delete('/operation-log/clear', 'OperationLog@clear')
                ->name('larke-admin.operation-log.clear');
            
            $router->delete('/operation-log/{id}', 'OperationLog@delete')
                ->name('larke-admin.operation-log.delete')
                ->where('id', '[A-Za-z0-9\-]+');
        });
});