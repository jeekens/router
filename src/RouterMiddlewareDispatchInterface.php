<?php declare(strict_types=1);


namespace Jeekens\Router;



/**
 * Interface RouterMiddlewareDispatchInterface
 *
 * @package Jeekens\Router
 */
interface RouterMiddlewareDispatchInterface
{

    /**
     * 注册中间件
     *
     * @param string $groupName
     * @param $middleware
     *
     * @return mixed
     */
    public function addMiddleware(string $groupName, $middleware);

    /**
     * 调用中间件
     *
     * @param RouterTreeNodeInterface $node
     * @param RouterInterface $router
     *
     * @return mixed
     */
    public function callMiddleware(RouterTreeNodeInterface $node, RouterInterface $router);

}