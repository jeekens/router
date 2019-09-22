<?php declare(strict_types=1);


namespace Jeekens\Router;


use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function implode;
use function is_array;
use function is_string;
use function sprintf;
use function uniqid;

/**
 * Class Router
 *
 * @package Jeekens\Router
 */
class Router implements RouterInterface
{

    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    /**
     * @var array|null
     */
    protected static $pattern;

    /**
     * @var null|string
     */
    protected $groupName = 'global';

    /**
     * @var array
     */
    protected $scheme = [self::SCHEME_HTTP, self::SCHEME_HTTPS];

    /**
     * @var string
     */
    protected $domain = '.*';

    /**
     * @var null|string
     */
    protected $port = '(:[0-9]+)?';

    /**
     * @var RouterDispatchInterface
     */
    protected $dispatch;

    /**
     * @var RouterMiddlewareDispatchInterface
     */
    protected $middleware;

    /**
     * @var RouterTreeInterface
     */
    protected $routerTree;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var array|null
     */
    protected $param;

    /**
     * @var array
     */
    protected $groupOptions = null;


    /**
     * @var string
     */
    protected $routerTreeClass = RouterTree::class;

    /**
     * @var string
     */
    protected $routerDispatchClass = RouterDispatch::class;

    /**
     * @var string
     */
    protected $routerMiddlewareClass = RouterMiddlewareDispatch::class;


    public function any(string $path, $handle)
    {
        return $this->match([self::METHOD_POST, self::METHOD_GET], $path, $handle);
    }


    public function delete(string $path, $handle)
    {
        return $this->match([self::METHOD_DELETE], $path, $handle);
    }

    /**
     * 调度路由
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws Exception\RouterNotFoundException
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $tree = $this->getRouterTree();
        $scheme = $request->getUri()->getScheme();
        $port = '';
        $path = '/';

        if ($request->getUri()->getPort()) {
            $port = ':' . $request->getUri()->getPort();
        } elseif ($scheme === self::SCHEME_HTTP) {
            $port = ':80';
        } elseif ($scheme === self::SCHEME_HTTPS) {
            $port = ':443';
        }

        if (!empty($request->getUri()->getPath())) {
            $path = $request->getUri()->getPath();
        }

        $node = $tree->lookup(
            $request->getMethod(),
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $port,
            $path,
            $this
        );

        if ($node
            && ($response = $this->getMiddleware()->callMiddleware($node, $this))
            && $response instanceof ResponseInterface
        ) {
            return $response;
        }

        return $this->getRouterDispatch()
            ->execute($node, $this);
    }


    public function get(string $path, $handle)
    {
        return $this->match([self::METHOD_GET], $path, $handle);
    }


    public function getParam(): ?array
    {
        return $this->param;
    }


    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }


    public function group(?array $options, Closure $closure)
    {
        $parentOpt = $this->setGroupOptions($options);
        $this->groupCall($closure);
        $this->registerGroupMiddleware();
        $this->resetGroupOptions($parentOpt);
    }


    public function match($method, string $path, $handle)
    {
        if (is_string($method)) {
            $methods = [$method];
        } else {
            $methods = [$method];
        }

        $tree = $this->getRouterTree();
        $node = $tree->addPatch($methods, $path, $this);
        $this->getRouterDispatch()
            ->addDispatch($node, $handle);
    }


    public function notfound($handle)
    {
        $this->getRouterDispatch()->addNotFoundDispatch($handle);
    }


    public function param($name, $default = null)
    {
        return $this->param[$name] ?? $default;
    }


    public function post(string $path, $handle)
    {
        $this->match([self::METHOD_POST], $path, $handle);
    }


    public function put(string $path, $handle)
    {
        $this->match([self::METHOD_PUT], $path, $handle);
    }


    public function registerDispatch(RouterDispatchInterface $dispatch): RouterInterface
    {
        if ($this->dispatch === null) {
            $this->dispatch = $dispatch;
        }

        return $this;
    }


    public function registerMiddleware($middleware): RouterInterface
    {
        $this->getMiddleware()->addMiddleware($this->getGroupName(), $middleware);

        return $this;
    }


    public function registerMiddlewareDispatch(RouterMiddlewareDispatchInterface $dispatch): RouterInterface
    {
        if ($this->middleware === null) {
            $this->middleware = $dispatch;
        }

        return $this;
    }


    public function registerRouterTree(RouterTreeInterface $tree): RouterInterface
    {
        if ($this->routerTree === null) {
            $this->routerTree = $tree;
        }

        return $this;
    }


    public function setParam(array $param): RouterInterface
    {
        $this->param = $param;

        return $this;
    }


    public function addParam($name, $value): RouterInterface
    {
        $this->param[$name] = $value;

        return $this;
    }


    public static function pattern(string $name, string $pattern)
    {
        self::$pattern[$name] = $pattern;
    }


    public function getMiddleware(): RouterMiddlewareDispatchInterface
    {
        if ($this->middleware === null) {
            $routerMiddlewareClass = $this->routerMiddlewareClass;
            $this->middleware = new $routerMiddlewareClass();
        }

        return $this->middleware;
    }


    public function getRouterDispatch(): RouterDispatchInterface
    {
        if ($this->dispatch === null) {
            $routerDispatchClass = $this->routerDispatchClass;
            $this->dispatch = new $routerDispatchClass();
        }

        return $this->dispatch;
    }


    public function getRouterTree(): RouterTreeInterface
    {
        if ($this->routerTree === null) {
            $routerTreeClass = $this->routerTreeClass;
            $this->routerTree = new $routerTreeClass();
        }

        return $this->routerTree;
    }

    /**
     * 替换正则
     *
     * @param string $replacement
     *
     * @return string
     */
    public function replacePattern(string $replacement): string
    {

    }


    public function groupCall(Closure $closure)
    {
        $closure($this);
    }


    public function addGroupMiddleware(string $groupName, $middleware): RouterInterface
    {
       return $this;
    }


    /**
     * 获取当前路由分组名
     *
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupOptions['groupName'] ?? $this->groupName;
    }

    /**
     * @return string
     */
    protected function getScheme(): string
    {
        if (empty($this->$this->groupOptions['scheme'])) {
            return implode('|', $this->scheme);
        } else {
            if (is_string($this->groupOptions['scheme'])) {
                return $this->groupOptions['scheme'];
            } else {
                return implode('|', $this->groupOptions['scheme']);
            }
        }
    }

    /**
     * @return string
     */
    protected function getDomain(): string
    {
        if (empty($this->groupOptions['domain'])) {
            return $this->domain;
        } else {
            return $this->replacePattern($this->groupOptions['domain']);
        }
    }

    /**
     * @return string
     */
    protected function getPort(): string
    {
        if (empty($this->groupOptions['port'])) {
            return $this->port;
        } else {
            if (is_array($this->groupOptions['port'])) {
                return sprintf('(:%s)?', implode('|', $this->groupOptions['port']));
            } else {
                return "(:{$this->groupOptions['port']})?";
            }
        }
    }

    /**
     * 重置分组设置
     *
     * @param array|null $options
     */
    protected function resetGroupOptions(?array $options)
    {
        if ($options === null) {
            $this->groupOptions = null;
        } else {
            $this->setGroupOptions($options, true);
        }
    }

    /**
     * 分组设置
     *
     * @param array|null $options
     * @param bool $reSet
     *
     * @return array
     */
    protected function setGroupOptions(?array $options, bool $reSet = false): array
    {
        $parentOpt = $this->groupOptions;

        if (!$reSet) {
            $uuid = uniqid('', true);
            if (empty($options['groupName'])) {
                $options['groupName'] = 'router_group_'.$uuid;
            } else {
                $options['groupName'] = $options['groupName'].'_'.$uuid;
            }
        }

        if (!empty($options['groupName'])) {
            $this->groupOptions['groupName'] = $options['groupName'];
        }

        if (!empty($options['domain'])) {
            $this->groupOptions['domain'] = $options['domain'];
        }

        if (!empty($options['scheme'])) {
            $this->groupOptions['scheme'] = $options['scheme'];
        }

        if (!empty($options['port'])) {
            $this->groupOptions['port'] = $options['port'];
        }

        if (!empty($options['middleware'])) {
            $this->groupOptions['middleware'] = $options['middleware'];
        }

        return $parentOpt;
    }

    /**
     * 注册分组中间件
     */
    protected function registerGroupMiddleware()
    {
        if (!empty($this->groupOptions['middleware'])) {
            $this->registerMiddleware($this->groupOptions['middleware']);
        }
    }

}