<?php declare(strict_types=1);


namespace Jeekens\Router;


use function array_filter;
use function array_values;

/**
 * Class RouterTree
 *
 * @package Jeekens\Router
 */
class RouterTree implements RouterTreeInterface
{

    protected $nodeClass = RouterTreeNode::class;

    /**
     * @var RouterTreeNodeInterface
     */
    protected $rootNode;

    /**
     * @var int
     */
    protected $startIndex = 0;

    /**
     * @var array
     */
    protected $tree = [];


    public function addPatch(array $method, string $path, RouterInterface $router): RouterTreeNodeInterface
    {
        $rootNode = $this->rootNode();
        if ($path === '/') {
            $rootNode->setMethods($method);
            $rootNode->setGroupNames($router);
            $rootNode->setPattern($router, '/');
            $rootNode->setIsLeaf();
            return $rootNode;
        } else {
            $node = $this->newNode();
            $node->setMethods($method);
            $node->setPattern($router, $path);
            $node->setIsLeaf(true);
            $rootNode->addSubNode($node, $this);
            return $node;
        }
    }


    public function getRouterTreeNode(int $nodeId): ?RouterTreeNodeInterface
    {
        return $this->tree[$nodeId]['node'] ?? null;
    }


    public function lookup(string $method, string $scheme, string $domain, string $port, string $path, RouterInterface $router): ?RouterTreeNodeInterface
    {
        // TODO: Implement lookup() method.
    }


    public function getSubNode(int $nodeId): ?array
    {
        return array_values(array_filter($this->tree, function ($arr) use ($nodeId) {
            return $arr['pid'] === $nodeId;
        }));
    }


    public function newNode(): RouterTreeNodeInterface
    {
        $node = new $this->nodeClass;
        $node->setNodeId($this->startIndex++);
        return $node;
    }

    /**
     * 获取根节点
     *
     * @return RouterTreeNodeInterface
     */
    protected function rootNode(): RouterTreeNodeInterface
    {
        if ($this->rootNode === null) {
            $this->rootNode = $this->newNode();
            $this->treeHelper($this->rootNode);
        }

        return $this->rootNode;
    }

    /**
     * 树形结构数据生成助手
     *
     * @param RouterTreeNodeInterface $node
     */
    protected function treeHelper(RouterTreeNodeInterface $node)
    {
        if (isset($this->treeIndex[$node->nodeId()])) {
            $this->tree[$node->nodeId()]['pid'] = $node->parentNodeId();
        } else {
            $this->tree[$node->nodeId()] = [
                'id' => $node->nodeId(),
                'pid' => $node->parentNodeId(),
                'node' => $node,
            ];
        }
    }

}