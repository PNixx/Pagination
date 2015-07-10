Pagination
==========

Pagination for Foundation framework is a simple to use class that provides pagination links for your app. You can customize the
look and feel of the pages links by writing a custom renderer or use one of the available ones like Twitter's
Bootstrap or extend them.

Install
-------

If you are using [composer](https://getcomposer.org/) type in the shell, otherwise you have to download Pagination and
Exception classes


```bash
composer require pnixx/pagination
```


Basic usage
-----------

Pagination class by it's own does not have any renders, instead it's primary goal is to give an array of items (links).
This can be done easily:

```php
<?php

use PNixx\Pagination\Pagination;
$pagination = new Pagination();

// Set the total number of items
$pagination->setTotalItems(45);

// Render html
echo $pagination->render(); 
```

Basic pagination rendering html, like this:
```html
<ul class="pagination">
  <li class="arrow unavailable"><a href="">&laquo;</a></li>
  <li class="current"><a href="">1</a></li>
  <li><a href="">2</a></li>
  <li><a href="">3</a></li>
  <li><a href="">4</a></li>
  <li class="unavailable"><a href="">&hellip;</a></li>
  <li><a href="">12</a></li>
  <li><a href="">13</a></li>
  <li class="arrow"><a href="">&raquo;</a></li>
</ul>
```

Setters
-------
```php
<?php

// Total number of items. This one MUST be set.
$pagination->setTotalItems($total_items);

// setTotalItems() alias
$pagination->setItems($total_items);

// Sets the number of items (lines) in a single page.
$pagination->setItemsPerPage($per_page);

// setItemsPerPage() alias
$pagination->setLimit($per_page);

// Sets the page number manually.
$pagination->setPage($page);

/*
 * Sets the URI pattern for creating links for pages.
 * Default pattern is "page={page}"  (URLs like /posts/show?page=5)
 * Can be set for example to "p={page}" or anything else for $_GET parameter
 * Can be set also to "page/{page}" for friendly URLs. In this case Pagination
 * will build URLs like: /posts/show/page/5
 */
$pagination->setPattern($pattern);

/*
 * Sets the current URI. Default is $_SERVER["REQUEST_URI"]
 * Handy for unit tests.
 */
$pagination->setUri($uri);

// Sets the proximity. See getProximity() above for more explanations.
$pagination->setProximity($proximity);

// Prev and Next labels
$pagination->setLabelPrev($label_prev);
$pagination->setLabelNext($label_next);

// Show arrows always or only not on the first or last page. Default: true (always)
$pagination->setShowArrowIfNeed($show_arrow_if_need);
```

Each setting can be done in the Pagination constructor.

```php
<?php

$pagination = new Pagination([
    'items' => 100, // or 'total'
    'per_page' => 10,
    'proximity' => 3,
    'uri' => 'http://example.com/show/page:6',
    'pattern' => 'page:{page}',
    'page' => 6,
])
```
