<?php
/**
 * Class InlineFragment
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Inline_Fragment
{
	use GraphQL_Client_Field_Trait;

	/**
	 * Stores the format for the inline fragment format
	 *
	 * @var string
	 */
	protected const FORMAT = '... on %s%s';

	/**
	 * @var string
	 */
	protected string $type_name;

	/**
	 * @var GraphQL_Client_Query_Builder_Interface|null
	 */
	protected ?GraphQL_Client_Query_Builder_Interface $query_builder;

	/**
	 * InlineFragment constructor.
	 *
	 * @param  string  $type_name
	 * @param  GraphQL_Client_Query_Builder_Interface|null  $query_builder
	 */
	public function __construct(string $type_name, ?GraphQL_Client_Query_Builder_Interface $query_builder = null)
	{
		$this->type_name = $type_name;
		$this->query_builder = $query_builder;
	}

	/**
	 *
	 */
	public function __toString()
	{
		if ($this->query_builder !== null) {
			$this->set_selection_set($this->query_builder->get_query()->get_selection_set());
		}

		return sprintf(static::FORMAT, $this->type_name, $this->construct_selection_set());
	}
}
