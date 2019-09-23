<?php declare(strict_types=1);


namespace Jeekens\Router;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function end;
use function explode;
use function is_int;
use function is_string;
use function preg_match;
use function str_replace;

/**
 * Class RouterTreeNode
 *
 * @package Jeekens\Router
 */
class RouterTreeNode implements RouterTreeNodeInterface
{

    /**
     * @var int
     */
    protected $nodeId = 0;

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var array
     */
    protected $groupNames = [];

    /**
     * @var string
     */
    protected $pattern = '';

    /**
     * @var string
     */
    protected $nodePattern = '';

    /**
     * @var string
     */
    protected $matchPattern = '';

    /**
     * @var bool
     */
    protected $isLeaf = false;

    /**
     * @var int
     */
    protected $parentNodeId = 0;

    /**
     * @var bool
     */
    protected $patternIsString = false;

    /**
     * @var null|array
     */
    protected $patch = null;


    public function addSubNode(RouterTreeNodeInterface $node, RouterTreeInterface $tree)
    {
        $subNodes = $tree->getSubNode($this->nodeId());

        if (empty($subNodes)) {
            $node->setParentNodeId($this->nodeId());
        }

    }


    public function setGroupNames(array $names)
    {
        $this->groupNames = $names;
    }


    public function setMethods($methods)
    {
        if (is_string($methods)) {
            $this->groupNames = [$methods];
        } else {
            $this->groupNames = $methods;
        }
    }


    public function setNodeId(int $nodeId)
    {
        $this->nodeId = $nodeId;
    }


    public function setPattern(RouterInterface $router, string $pattern)
    {
        if (!$this->pattern) {
            $this->pattern = $pattern;
            $path = $this->patch = explode('/', $pattern);
        }

        if ($pattern === '/') {
            $this->nodePattern = '/';
            $this->matchPattern = '/';
        } else {
            if(!isset($path)) $path = explode('/', $pattern);
            $this->matchPattern = $router->replacePattern($this->nodePattern);
        }

        if ($this->matchPattern === $this->nodePattern) {
            $this->patternIsString = true;
        }
    }


    public function setIsLeaf(bool $bool = true)
    {
        $this->isLeaf = $bool;
    }


    public function setParentNodeId(int $parentNodeId)
    {
        $this->parentNodeId = $parentNodeId;
    }


    public function methods(): array
    {
        return $this->methods;
    }


    public function groupNames(): array
    {
        return $this->groupNames;
    }


    public function patch(): ?array
    {
        return $this->patch;
    }

    public function nodeId(): int
    {
        return $this->nodeId;
    }


    public function nodePattern(): string
    {
        return $this->nodePattern;
    }


    public function pattern(): string
    {
        return $this->pattern;
    }


    public function parentNodeId(): int
    {
        return $this->parentNodeId;
    }


    public function isLeaf(): bool
    {
        return $this->isLeaf;
    }


    public function compare(string $path, &$data): bool
    {
        $data = null;

        if ($this->patternIsString) {
            return $this->matchPattern === $path;
        } elseif (preg_match($this->matchPattern, $path, $tmp)) {
            $data = array_filter($tmp, function ($k) {
                return !is_int($k);
            }, ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    /**
     * @param array $param
     *
     * @return string
     */
    public function url(array $param = []): string
    {
        if (empty($param)) {
            return $this->pattern;
        }

        return str_replace(array_map(function ($v) {
                return '{'.$v.'}';
            },
            array_keys($param)
        ),
            array_values($param),
            $this->pattern
        );
    }

}