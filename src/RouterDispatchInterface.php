<?php declare(strict_types=1);


namespace Jeekens\Router;


use Jeekens\Router\Exception\RouterNotFoundException;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouterDispatchInterface
 *
 * @package Jeekens\Router
 */
interface RouterDispatchInterface
{

    /**
     * 添加一个节点调度信息
     *
     * @param RouterTreeNodeInterface $node
     * @param $handle
     *
     * @return mixed
     */
    public function addDispatch(RouterTreeNodeInterface $node, $handle);

    /**
     * 传入一个路由对象，并执行一个路由调度
     *
     * @param RouterTreeNodeInterface $node
     * @param RouterInterface $router
     *
     * @throws RouterNotFoundException
     *
     * @return ResponseInterface
     */
    public function execute(?RouterTreeNodeInterface $node, RouterInterface $router): ResponseInterface;

    /**
     * 添加404路由处理方法
     *
     * @param $handle
     */
    public function addNotFoundDispatch($handle);

}