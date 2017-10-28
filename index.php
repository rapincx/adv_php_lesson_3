<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Parse\ParseContent;

$parser = new ParseContent('shop');
$parser->init()->setPageCount(33)->parseLinksItems( 'http://olliplus.com.ua/index.php?route=product/category&path=59&page=');
$parser->displayContentTable();