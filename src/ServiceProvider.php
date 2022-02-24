<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

use Larke\Admin\Extension\Rule;
use Larke\Admin\Extension\ServiceProvider as BaseServiceProvider;
use Larke\Admin\Frontend\Support\Menu;

// 文件夹
use Larke\Admin\OperationLog\Model;
use Larke\Admin\OperationLog\Observer;
use Larke\Admin\OperationLog\Middleware;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * 扩展信息
     */
    public $info = [
        'name' => 'larke/operation-log',
        'title' => '操作日志',
        'description' => '记录 admin 系统的相关操作日志',
        'keywords' => [
            'operation-log',
        ],
        'homepage' => 'https://github.com/deatil/larke-operation-log',
        'authors' => [
            [
                'name' => 'deatil', 
                'email' => 'deatil@github.com', 
                'homepage' => 'https://github.com/deatil', 
            ],
        ],
        'version' => '1.0.1',
        'adaptation' => '^1.3',
    ];
    
    /**
     * 扩展图标
     */
    public $icon = __DIR__ . '/../icon.png';

    /**
     * 路由中间件
     *
     * @var array
     */
    protected $routeMiddleware = [
        'larke-admin.operation-log' => Middleware\OperationLog::class,
    ];

    /**
     * 中间件分组
     *
     * @var array
     */
    protected $middlewareGroups = [
        'larke-admin' => [
            'larke-admin.operation-log',
        ],
    ];
    
    protected $slug = 'larke-admin.ext.operation-log';
    
    /**
     * 初始化
     */
    public function boot()
    {
        // 扩展注册
        $this->withExtension(
            $this->info['name'], 
            $this->withExtensionInfo(
                __CLASS__, 
                $this->info, 
                $this->icon
            )
        );
        
        // 事件
        $this->bootListeners();
        
        // 中间件
        $this->registerRouteMiddleware();
        
        // 模型事件
        $this->bootObserver();
    }
    
    /**
     * 运行中
     */
    public function start()
    {
        // 路由
        $this->loadRoutesFrom(__DIR__ . '/../resources/route/admin.php');
    }
    
    /**
     * 中间件
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middlewares) {
            foreach ($middlewares as $middleware) {
                app('router')->pushMiddlewareToGroup($key, $middleware);
            }
        }
    }

    /**
     * 模型事件
     *
     * @return void
     */
    protected function bootObserver()
    {
        Model\OperationLog::observe(new Observer\OperationLog());
    }
    
    /**
     * 推送
     */
    protected function assetsPublishes()
    {
        $this->publishes([
            __DIR__.'/../resources/assets/operation-log' => public_path('extension/operation-log'),
        ], 'larke-operation-log-assets');
        
        Artisan::call('vendor:publish', [
            '--tag' => 'larke-operation-log-assets',
            '--force' => true,
        ]);
    }
    
    /**
     * 监听器
     */
    public function bootListeners()
    {
        $thiz = $this;
        
        // 安装后
        $this->onInatll(function ($name, $info) use($thiz) {
            if ($name == $thiz->info["name"]) {
                $thiz->install();
            }
        });
        
        // 卸载后
        $this->onUninstall(function ($name, $info) use($thiz) {
            if ($name == $thiz->info["name"]) {
                $thiz->uninstall();
            }
        });
        
        // 更新后
        $this->onUpgrade(function ($name, $oldInfo, $newInfo) use($thiz) {
            if ($name == $thiz->info["name"]) {
                $thiz->upgrade();
            }
        });
        
        // 启用后
        $this->onEnable(function ($name, $info) use($thiz) {
            if ($name == $thiz->info["name"]) {
                $thiz->enable();
            }
        });
        
        // 禁用后
        $this->onDisable(function ($name, $info) use($thiz) {
            if ($name == $thiz->info["name"]) {
                $thiz->disable();
            }
        });
    }
    
    /**
     * 执行 sql
     */
    protected function runSql($file)
    {
        $sqlData = File::get($file);
        if (! empty($sqlData)) {
            $dbPrefix = DB::getConfig('prefix');
            $sqlContent = str_replace('pre__', $dbPrefix, $sqlData);
            
            DB::unprepared($sqlContent);
        }
    }
    
    /**
     * 安装后
     */
    protected function install()
    {
        $slug = $this->slug;
        
        $rules = include __DIR__ . '/../resources/rules/rules.php';
        
        // 添加权限
        Rule::create($rules);
        
        // 添加菜单
        Menu::create($rules);
        
        $this->assetsPublishes();
        
        // 执行数据库
        $sqlFile = __DIR__.'/../resources/database/install.sql';
        $this->runSql($sqlFile);
    }
    
    /**
     * 卸载后
     */
    protected function uninstall()
    {
        // 删除权限
        Rule::delete($this->slug);
        
        // 删除菜单
        Menu::delete($this->slug);
        
        // 执行数据库
        $sqlFile = __DIR__.'/../resources/database/uninstall.sql';
        $this->runSql($sqlFile);
    }
    
    /**
     * 更新后
     */
    protected function upgrade()
    {}
    
    /**
     * 启用后
     */
    protected function enable()
    {
        // 启用权限
        Rule::enable($this->slug);
        
        // 启用菜单
        Menu::enable($this->slug);
    }
    
    /**
     * 禁用后
     */
    protected function disable()
    {
        // 禁用权限
        Rule::disable($this->slug);
        
        // 禁用菜单
        Menu::disable($this->slug);
    }
    
}
