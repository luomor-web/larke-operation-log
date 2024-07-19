<?php

declare (strict_types = 1);

namespace Larke\Admin\OperationLog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

use Larke\Admin\Extension\Rule;
use Larke\Admin\Extension\Menu;
use Larke\Admin\Extension\ServiceProvider as BaseServiceProvider;

// 文件夹
use Larke\Admin\OperationLog\Model;
use Larke\Admin\OperationLog\Observer;
use Larke\Admin\OperationLog\Middleware;

use function Larke\Admin\register_install_hook;
use function Larke\Admin\register_uninstall_hook;
use function Larke\Admin\register_upgrade_hook;
use function Larke\Admin\register_enable_hook;
use function Larke\Admin\register_disable_hook;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * composer
     */
    public $composer = __DIR__ . '/../composer.json';

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
    
    // 包名
    protected $pkg = "larke/operation-log";
    
    protected $slug = 'larke-admin.ext.operation-log';
    
    /**
     * 初始化
     */
    public function boot()
    {
        // 扩展注册
        $this->addExtension(
            name:     __CLASS__, 
            composer: $this->composer,
            icon:     $this->icon,
        );
    }
    
    /**
     * 在扩展安装、扩展卸载等操作时有效
     */
    public function action()
    {
        register_install_hook($this->pkg, [$this, 'install']);
        register_uninstall_hook($this->pkg, [$this, 'uninstall']);
        register_upgrade_hook($this->pkg, [$this, 'upgrade']);
        register_enable_hook($this->pkg, [$this, 'enable']);
        register_disable_hook($this->pkg, [$this, 'disable']);
    }

    /**
     * 运行中
     */
    public function start()
    {
        // 路由
        $this->loadRoutesFrom(__DIR__ . '/../resources/route/admin.php');
        
        // 中间件
        $this->registerRouteMiddleware();
        
        // 模型事件
        $this->bootObserver();
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
