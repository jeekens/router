<?php declare(strict_types=1);


namespace Jeekens\Router;

/**
 * Interface RouterTreeNodeInterface
 *
 * @package Jeekens\Router
 */
interface RouterTreeNodeInterface
{

    /**
     * 添加子节点
     *
     * @param RouterTreeNodeInterface $node
     */
    public function addSubNode(RouterTreeNodeInterface $node);

    /**
     * 设置路由节点id
     *
     * @param $nodeId
     */
    public function setNodeId(int $nodeId);


    /**
     * 设置父级路由节点id
     *
     * @param int $parentNodeId
     */
    public function setParentNodeId(int $parentNodeId);

    /**
     * 设置节点分组名
     *
     * @param string|array $names
     */
    public function setGroupNames(array $names);

    /**
     *
     * 设置支持的请求方法
     *
     * @param array $methods
     */
    public function setMethods(array $methods);

    /**
     * 设置url匹配规则
     *
     * @param string $pattern
     * @param RouterInterface $router
     */
    public function setPattern(RouterInterface $router, string $pattern);

    /**
     * 设置是否为叶子节点
     *
     * @param bool $bool
     */
    public function setIsLeaf(bool $bool = true);

    /**
     * 返回节点id
     *
     * @return int
     */
    public function nodeId(): int;

    /**
     * 返回节点分组名数组
     *
     * @return array
     */
    public function groupNames(): array;

    /**
     * 获取路由节点支持的请求方法
     *
     * @return array
     */
    public function methods(): array;

    /**
     * 获取url匹配规则
     *
     * @return string
     */
    public function pattern(): string;

    /**
     * 获取节点匹配规则
     *
     * @return string
     */
    public function nodePattern(): string;

    /**
     * 是否为叶子节点
     *
     * @return bool
     */
    public function isLeaf(): bool;

    /**
     * 根据传入的参数返回一个路由节点的链接
     *
     * @param array $param
     *
     * @return string
     */
    public function url(array $param = []): string;

    /**
     * 获取父级节点id
     *
     * @return int
     */
    public function parentNodeId(): int;

    /**
     * 对比路径串并将匹配到的是值赋值给data变量
     *
     * @param string $path
     * @param array|null $data
     *
     * @return bool
     */
    public function compare(string $path, ?array &$data): bool;

}