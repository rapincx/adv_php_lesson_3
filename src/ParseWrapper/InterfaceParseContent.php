<?php

namespace App\Parse\ParseWrapper;

interface InterfaceParseContent
{
	public function parseContentItem($itemLink);
	public function parseContentAllItem($baseUrl, $offset, $limit);
	public function displayContent();
	public function displayContentTable();
	public function saveContent($type);
}