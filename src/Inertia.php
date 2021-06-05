<?php

namespace Inertia;

use SilverStripe\View\TemplateGlobalProvider;

class Inertia implements TemplateGlobalProvider
{
    public static function render($component = null, $props = [])
    {
        $inertia = new ResponseFactory();

        if ($component) {
            return $inertia->render($component, $props);
        }

        return $inertia;
    }

    public static function renderApp($pageJson)
    {
        return "<div id='app' data-page='{$pageJson}'></div>";
    }

    public static function get_template_global_variables()
    {
        return [
            'inertia' => [
                'method' => 'renderApp',
                'casting' => 'HTMLText'
            ]
        ];
    }
}
