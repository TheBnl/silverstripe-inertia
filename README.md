# WIP Inertia server-side adapter for SilverStripe
_This module is a still Work In progress, and help/pr's would be much appreciated!_

Inertia allows you to create fully client-side rendered, single-page apps, without much of the complexity that comes with modern SPAs. It does this by leveraging existing server-side frameworks.

[Read more about Inertia](https://inertiajs.com/)

## Installation
Install the module trough composer:
```bash
composer require bramdeleeuw/silverstripe-inertia
``` 

Add $Inertia to your Page.ss layout. 
```html
$Inertia($pageJson)
```
This wil render the following snippet:
```html
<div id="app" data-page="{the page data}"></div>
```


In your controller you can now add the following method to populate your vue/react/etc. app.

```php
<?php

use SilverStripe\CMS\Controllers\ContentController;
use Inertia\Inertia;

class PageController extends ContentController
{
    public function index()
    {
        return Inertia::render('Page', [
            'title' => $this->Title,
            'content' => $this->Content
        ]);
    }
}
```
On initial request this will populate the `data-page` with the passed props. Following request made by Inertia will receive the passed props as json. 

### Maintainers 
[Bram de Leeuw](http://www.twitter.com/bramdeleeuw)
