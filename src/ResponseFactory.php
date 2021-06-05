<?php

namespace Inertia;

use Closure;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\SS_List;

class ResponseFactory
{
    protected $rootView = 'Page';

    protected $sharedProps = [];

    protected $version = null;

    public function setRootView($name)
    {
        $this->rootView = $name;
    }

    public function share($key, $value = null)
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } elseif($key instanceof SS_List) {
            $this->sharedProps = array_merge($this->sharedProps, $key->toArray());
        } else {
            Helpers::arraySet($this->sharedProps, $key, $value);
        }
    }

    public function getShared($key = null, $default = null)
    {
        if ($key) {
            return Helpers::arrayGet($this->sharedProps, $key, $default);
        }

        return $this->sharedProps;
    }

    public function flushShared()
    {
        $this->sharedProps = [];
    }

    public function version($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        $version = $this->version instanceof Closure
            ? Helpers::closureCall($this->version)
            : $this->version;

        return (string) $version;
    }

//    public function lazy(callable $callback)
//    {
//        return new LazyProp($callback);
//    }

    public function render($component, $props = [])
    {
        if ($props instanceof SS_List) {
            $props = $props->toArray();
        }

        $response = new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->rootView,
            $this->getVersion()
        );

        return $response->toResponse();
    }

    public function location($url)
    {
        $response = new HTTPResponse('', 409);
        $response->addHeader('X-Inertia-Location', $url);
        return $response;
    }
}
