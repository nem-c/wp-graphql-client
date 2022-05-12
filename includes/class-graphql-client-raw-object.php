<?php
/**
 * Class RawObject
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Raw_Object
{
	/**
	 * @var string
	 */
	protected string $object_string;

	/**
	 * JsonObject constructor.
	 *
	 * @param  string  $object_string
	 */
	public function __construct(string $object_string)
	{
		$this->object_string = $object_string;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->object_string;
	}
}
