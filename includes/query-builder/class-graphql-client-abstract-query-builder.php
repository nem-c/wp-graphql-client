<?php

/**
 * Class AbstractQueryBuilder
 *
 * @package GraphQL_Client
 */
abstract class GraphQL_Client_Abstract_Query_Builder implements GraphQL_Client_Query_Builder_Interface {
	/**
	 * @var GraphQL_Client_Query
	 */
	protected GraphQL_Client_Query $query;

	/**
	 * @var array|GraphQL_Client_Variable()
	 */
	private array $variables;

	/**
	 * @var array
	 */
	private array $selection_set;

	/**
	 * @var array
	 */
	private array $arguments_list;

	/**
	 * QueryBuilder constructor.
	 *
	 * @param  string  $query_object
	 * @param  string  $alias
	 */
	public function __construct( string $query_object = '', string $alias = '' ) {
		$this->query          = new GraphQL_Client_Query( $query_object, $alias );
		$this->variables      = array();
		$this->selection_set  = array();
		$this->arguments_list = array();
	}

	/**
	 * @param  string  $alias
	 *
	 * @return self
	 */
	public function set_alias( string $alias ): self {
		$this->query->set_alias( $alias );

		return $this;
	}

	/**
	 * @return GraphQL_Client_Query
	 */
	public function get_query(): GraphQL_Client_Query {
		// Convert nested query builders to query objects
		foreach ( $this->selection_set as $key => $field ) {
			if ( $field instanceof GraphQL_Client_Query_Builder_Interface ) {
				$this->selection_set[ $key ] = $field->get_query();
			}
		}

		$this->query->set_variables( $this->variables );
		$this->query->set_arguments( $this->arguments_list );
		$this->query->set_selection_set( $this->selection_set );

		return $this->query;
	}

	/**
	 * @param  string|GraphQL_Client_Query_Builder_Interface|GraphQL_Client_Inline_Fragment|GraphQL_Client_Query  $selected_field
	 *
	 * @return self
	 */
	protected function select_field( $selected_field ): self {
		if (
			is_string( $selected_field )
			|| $selected_field instanceof GraphQL_Client_Query_Builder_Interface
			|| $selected_field instanceof GraphQL_Client_Query
			|| $selected_field instanceof GraphQL_Client_Inline_Fragment
		) {
			$this->selection_set[] = $selected_field;
		}

		return $this;
	}

	/**
	 * @param  string  $argument_name
	 * @param  mixed  $argument_value
	 *
	 * @return self
	 */
	protected function set_argument( string $argument_name, $argument_value ): self {
		if ( is_scalar( $argument_value ) || is_array( $argument_value ) || $argument_value instanceof GraphQL_Client_Raw_Object ) {
			$this->arguments_list[ $argument_name ] = $argument_value;
		}

		return $this;
	}

	/**
	 * @param  string  $name
	 * @param  string  $type
	 * @param  bool  $is_required
	 * @param  null  $default_value
	 *
	 * @return self
	 */
	protected function set_variable( string $name, string $type, bool $is_required = false, $default_value = null ): self {
		$this->variables[] = new GraphQL_Client_Variable( $name, $type, $is_required, $default_value );

		return $this;
	}
}
