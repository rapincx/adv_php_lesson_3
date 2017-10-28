<?php

namespace App\Parse\ParseWrapper;

/**
 * Class AbstractParseContent
 * @package App\Parse\AbstractParse
 */
abstract class AbstractParseContent
{
	/**
	 * @var string
	 */
	private $typeParseContent;

	/**
	 *
	 * AbstractParseContent constructor.
	 * @param $typeParseContent
	 */
	public function __construct($typeParseContent)
	{
		$this->typeParseContent = $typeParseContent;
	}
}