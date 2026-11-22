<?php
class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [$method, $regex, $handler];
    }
    public function get(string $p, callable $h): void { $this->add('GET', $p, $h); }
    public function post(string $p, callable $h): void { $this->add('POST', $p, $h); }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim((string)parse_url($uri, PHP_URL_PATH), '/');
        if ($path === '') $path = '/';
        if ($method === 'HEAD') $method = 'GET';
        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m !== $method) continue;
            if (preg_match($regex, $path, $mt)) {
                $params = [];
                foreach ($mt as $k => $v) if (is_string($k)) $params[] = urldecode($v);
                $handler(...$params);
                return;
            }
        }
        abort(404);
    }
}
