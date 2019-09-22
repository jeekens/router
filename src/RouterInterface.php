<?php declare(strict_types=1);


namespace Jeekens\Router;


use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RouterInterface
 *
 * @package Jeekens\Router
 */
interface RouterInterface
{

    /**
     * 注册路由调度器
     *
     * @param RouterDispatchInterface $dispatch
     */
    public function registerDispatch(RouterDispatchInterface $dispatch);

    /**
     * 注册中间件调度器
     *
     * @param RouterMiddlewareDispatchInterface $dispatch
     */
    public function registerMiddlewareDispatch(RouterMiddlewareDispatchInterface $dispatch);

    /**
     * 注册路由树
     *
     * @param RouterTreeInterface
     */
    public function registerRouterTree(RouterTreeInterface $tree);

    /**
     * 注册全局中间件
     *
     * @param array $middleware
     */
    public function registerMiddleware($middleware);

    /**
     * 添加分组公共中间件
     *
     * @param string $groupName
     * @param $middleware
     */
    public function addGroupMiddleware(string $groupName, $middleware);

    /**
     * 设置 post & get & put & delete 路由
     *
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function any(string $path, $handle);

    /**
     * 设置post路由
     *
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function post(string $path, $handle);

    /**
     * 设置get路由
     *
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function get(string $path, $handle);

    /**
     * 设置put路由
     *
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function put(string $path, $handle);

    /**
     * 设置delete路由
     *
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function delete(string $path, $handle);

    /**
     * 批量设置路由
     *
     * @param string|array $method
     * @param string $path
     * @param $handle
     *
     * @return mixed
     */
    public function match($method, string $path, $handle);

    /**
     * 路由分组
     *
     * @param array|null $options
     * @param Closure $closure
     *
     * @return mixed
     */
    public function group(?array $options, Closure $closure);

    /**
     * 注册一个notfound路由
     *
     * @param $handle
     *
     * @return mixed
     */
    public function notfound($handle);

    /**
     * 传入调度信息并进行路由调度
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface;

    /**
     * 获取路由request对象
     *
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ServerRequestInterface;

    /**
     * 设置路由参数
     *
     * @param array $param
     */
    public function setParam(array $param);

    /**
     * 获取全部路由参数
     *
     * @return array|null
     */
    public function getParam(): ?array;

    /**
     * 设置路由参数
     *
     * @param $name
     * @param $value
     */
    public function addParam($name, $value);

    /**
     * 获取单项路由参数
     *
     * @param $name
     * @param null $default
     *
     * @return mixed
     */
    public function param($name, $default = null);

    /**
     * 获取路由调度器
     *
     * @return RouterDispatchInterface
     */
    public function getRouterDispatch(): RouterDispatchInterface;

    /**
     * 获取中间件调度器
     *
     * @return RouterMiddlewareDispatchInterface
     */
    public function getMiddleware(): RouterMiddlewareDispatchInterface;

    /**
     * 获取路由树
     *
     * @return RouterTreeInterface
     */
    public function getRouterTree(): RouterTreeInterface;

    /**
     * 设置匹配规则
     *
     * @param string $name
     * @param string $pattern
     *
     * @return mixed
     */
    public static function pattern(string $name, string $pattern);

    /**
     * 替换匹配规则为正则
     *
     * @param string $replacement
     *
     * @return string
     */
    public function replacePattern(string $replacement);

    /**
     * 分组注册回调方法
     *
     * @param Closure $closure
     */
    public function groupCall(Closure $closure);

}