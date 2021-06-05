<?php

namespace Inertia;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\SS_List;

class Response
{
    protected $component;

    protected $props;

    protected $rootView;

    protected $version;

    protected $viewData = [];

    public function __construct($component, $props, $rootView = 'Page', $version = null)
    {
        $this->component = $component;
        $this->props = $props instanceof SS_List ? $props->toArray() : $props;
        $this->rootView = $rootView;
        $this->version = $version;
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    public function withViewData($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    // todo make silverstripe
    // should be called by controllers next step ?
    public function toResponse()
    {
        $request = $this->request();
        $partialData = $request->getHeader('X-Inertia-Partial-Data');
        $only = array_filter(
            explode(',', $partialData ? $partialData->getValue() : '')
        );

        $partialComponent = $request->getHeader('X-Inertia-Partial-Component');
        $props = ($only && ($partialComponent ? $partialComponent->getValue() : '') === $this->component)
            ? Helpers::arrayOnly($this->props, $only)
            : $this->props;

        array_walk_recursive($props, static function (&$prop) {
            $prop = Helpers::closureCall($prop);
        });

        $page = [
            'component' => $this->component,
            'props' => $props,
            'url' => $request->getURL(),
            'version' => $this->version ? $this->version : 0,
        ];
        $json = json_encode($page);

        $response = new HTTPResponse();
        if ($request->getHeader('X-Inertia')) {
            $response->setBody($json);
            $response->addHeader('Vary', 'Accept');
            $response->addHeader('X-Inertia','true');
            return $response;
        } else {
            $controller = Controller::curr();
            $processed = $controller->renderWith($this->rootView, $this->viewData + ['page' => $page, 'pageJson' => $json]);
            $response->setBody($processed);
            return $response;
        }
    }

    public function request()
    {
        $controller = Controller::curr();
        return $controller->getRequest();
    }
}
