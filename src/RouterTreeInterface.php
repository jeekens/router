<?php declare(strict_types=1);


namespace Jeekens\Router;

/**
 * Interface RouterTreeInterface
 *
 * @package Jeekens\Router
 */
interface RouterTreeInterface
{

    /**
     * 向路由树里面添加一个路由信息，并返回一个节点id
     *
     * @param array $method
     * @param string $path
     * @param RouterInterface $router
     *
     * @return RouterTreeNodeInterface
     */
    public function addPatch(
        array $method,
        string $path,
        RouterInterface $router
    ): RouterTreeNodeInterface;

    /**
     * 查找路由，如果找到匹配的路由则向路由中注入匹配的信息并返回一个路由节点.
     *
     * @param string $method
     * @param string $scheme
     * @param string $domain
     * @param string $port
     * @param string $path
     * @param RouterInterface $router
     *
     * @return RouterTreeNodeInterface|null
     */
    public function lookup(
        string $method,
        string $scheme,
        string $domain,
        string $port,
        string $path,
        RouterInterface $router
    ): ?RouterTreeNodeInterface;

    /**
     * 获取路由节点对象
     *
     * @param $nodeId
     *
     * @return RouterTreeNodeInterface|null
     */
    public function getRouterTreeNode(int $nodeId): ?RouterTreeNodeInterface;

    /**
     * 获取所有子节点
     *
     * @param $nodeId
     *
     * @return RouterTreeInterface[]|null
     */
    public function getSubNode(int $nodeId): ?array;

    /**
     * 创建一个新的节点
     *
     * @return RouterTreeNodeInterface
     */
    public function newNode(): RouterTreeNodeInterface;

}