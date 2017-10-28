<?php

namespace App\Parse;

use App\Parse\ParseWrapper\AbstractParseContent;
use App\Parse\ParseWrapper\InterfaceParseContent;

class ParseContent extends AbstractParseContent implements InterfaceParseContent
{
	private $baseUrl = '';
	private $pagesCount = 0;
	private $collectionItems = [];
	private $collectionLinkItems = [];
	private $contentPage;
	private $rulesParsePagesCount = '';
	private $rulesParseLinksItems = '';
	private $rulesParseItemData = [];

	public function init()
	{
		require_once __DIR__.'/../vendor/autoload.php';

		return $this;
	}

	public function setBaseUrl($baseUrl = '')
	{
		$this->baseUrl = $baseUrl;

		return $this;
	}

	public function setPageCount($pagesCount = 0)
	{
		$this->pagesCount = $pagesCount;

		return $this;
	}

	public function setRuleParsePageCount($selectorLink)
	{
		$this->rulesParsePagesCount = $selectorLink;

		return $this;
	}

	public function setRuleParseItemLink($selectorItemLink)
	{
		$this->collectionLinkItems = $selectorItemLink;

		return $this;
	}

	public function setRulesParseItemData($rules)
	{
		$this->rulesParseItemData = $rules;

		return $this;
	}

	public function parsePagesCount()
	{
		$element = pq($this->contentPage);
		$this->pagesCount = (int) $element->find($this->rulesParsePagesCount.':last-child')->text();

		return $this;
	}

	public function parseLinksItems($parseUrl, $ended = '', $limit = 0)
	{
		for($i = 1; $i <= $this->pagesCount; $i++){
			$this->parseContentItem($parseUrl.$i.$ended);
			$currentPage = pq($this->contentPage);
			$linksItems = $currentPage->find($this->rulesParseLinksItems)->attr('href');
			if((int) $limit === 0) {
				$this->collectionLinkItems[] = $linksItems;
			} else {
				$this->collectionLinkItems[] = array_slice($linksItems, $limit);
			}
		}

		return $this;
	}

	public function parseContentItem($itemLink)
	{
		$item = file_get_contents($itemLink);
		$this->contentPage = \phpQuery::newDocument($item);

		return $this;
	}

	public function parseItemData()
	{
		$data = [];
		$item = pq( $this->contentPage);
		foreach ($this->rulesParseItemData as $rule){
			$item->find($rule['selector']);
			$data[$rule['name']]['label'] = $rule['label'];
			$data[$rule['name']]['type'] = $rule['type'];
			if($rule['type'] === 'link') {
				$data[$rule['name']]['val'] = $item->attr('href');
			} elseif ($rule['type'] === 'img') {
				$data[$rule['name']]['val'] = $item->attr('src');
			} elseif ($rule['type'] === 'txt') {
				$data[$rule['name']]['val'] = $item->text();
			}
		}
		$this->collectionItems[] = $data;

		return $this;
	}

	public function parseContentAllItem($baseUrl, $offset = 0, $limit = 0)
	{
		$offset = (int) $offset;
		$limit = (int) $limit;
		$start = 0;
		foreach($this->collectionLinkItems as $link){
			if($start >= $offset && $start <= $limit) {
				$this->parseContentItem( $baseUrl . $link )->parseItemData();
			} else {
				break;
			}
			$start++;
		}

		return $this;
	}

	public function displayContent()
	{
		$table = '';
		foreach($this->collectionItems as $key=>$item){
			$table_item = '<tr><td><a href="'.$this->collectionLinkItems[$key].'"></a></td></tr>';
			$table_row = '';
			foreach($item as $data){
				if($data['type'] === 'link'){
					$data_val = '<a href="'.$data['val'].'">'.$data['val'].'</a>';
				} elseif($data['type'] === 'img'){
					$data_val = '<img src="'.$data['val'].'" alt="'.$data['label'].'">';
				} elseif($data['type'] === 'txt'){
					$data_val = '<p>'.$data['val'].'</p>';
				} else {
					$data_val = '';
				}
				$table_row = '<tr><td><strong>'.$data['label'].'</strong></td><td>'.$data_val.'</td></tr>';
			}
			$table .= $table_item.$table_row;
		}
		$table = '<table>'.$table.'</table>';

		echo $table;
	}

	public function displayContentTable()
	{
		$table = '';
		foreach($this->collectionItems as $key=>$item){
			$table_item = '<tr><td><a href="'.$this->collectionLinkItems[$key].'"></a></td></tr>';
			$table_row = '';
			foreach($item as $data){
				$table_row = '<tr><td><strong>'.$data['label'].'[</strong></td><td>'.$data['val'].'</td></tr>';
			}
			$table .= $table_item.$table_row;
		}
		$table = '<table>'.$table.'</table>';

		echo $table;
	}

	public function saveContent($type)
	{
		if($type === 'db'){
			$msg = 'Saved in DataBase';
		} elseif($type === 'csv'){
			$msg = 'Saved in CSV file';
		} else {
			$msg = 'Create Save method';
		}

		return $msg;
	}
}