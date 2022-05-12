<?php

/**
 * Class Query
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Query extends GraphQL_Client_Nestable_Object {
	use GraphQL_Client_Field_Trait;

	/**
	 * Stores the GraphQL query format
	 *
	 * First string is object name
	 * Second string is arguments
	 * Third string is selection set
	 *
	 * @var string
	 */
	protected const QUERY_FORMAT = '%s%s%s';

	/**
	 * Stores the name of the type of the operation to be executed on the GraphQL server
	 *
	 * @var string
	 */
	protected const OPERATION_TYPE = 'query';

	/**
	 * Stores the name of the operation to be run on the server
	 *
	 * @var string
	 */
	protected string $operation_name;

	/**
	 * Stores the object being queried for
	 *
	 * @var string
	 */
	protected string $field_name;

	/**
	 * Stores the object alias
	 *
	 * @var string
	 */
	protected string $alias;

	/**
	 * Stores the list of variables to be used in the query
	 *
	 * @var array|GraphQL_Client_Variable()
	 */
	protected array $variables;

	/**
	 * Stores the list of arguments used when querying data
	 *
	 * @var array
	 */
	protected array $arguments;

	/**
	 * @var array
	 */
	protected array $selection_set;

	/**
	 * Private member that's not accessible from outside the class, used internally to deduce if query is nested or not
	 *
	 * @var bool
	 */
	protected bool $is_nested;

	/**
	 * GQLQueryBuilder constructor.
	 *
	 * @param  string  $field_name
	 * @param  string  $alias  the alias to use for the query if required
	 */
	public function __construct( string $field_name = '', string $alias = '' ) {
		$this->field_name     = $field_name;
		$this->alias          = $alias;
		$this->operation_name = '';
		$this->variables      = array();
		$this->arguments      = array();
		$this->selection_set  = array();
		$this->is_nested      = false;
	}

	/**
	 * @param  string  $alias
	 *
	 * @return GraphQL_Client_Query
	 */
	public function set_alias( string $alias ): GraphQL_Client_Query {
		$this->alias = $alias;

		return $this;
	}

	/**
	 * @param  string  $operation_name
	 *
	 * @return GraphQL_Client_Query
	 */
	public function set_operation_name( string $operation_name ): GraphQL_Client_Query {
		if ( ! empty( $operation_name ) ) {
			$this->operation_name = " $operation_name";
		}

		return $this;
	}

	/**
	 * @param  array  $variables
	 *
	 * @return GraphQL_Client_Query
	 */
	public function set_variables( array $variables ): GraphQL_Client_Query {
		$non_var_elements = array_filter(
			$variables,
			function ( $e ) {
				return ! $e instanceof GraphQL_Client_Variable;
			}
		);
		if ( count( $non_var_elements ) > 0 ) {
			throw new GraphQL_Client_Invalid_Variable_Exception( 'At least one of the elements of the variables array provided is not an instance of GraphQL_Client_Variable' );
		}

		$this->variables = $variables;

		return $this;
	}

	/**
	 * Throwing exception when setting the arguments if they are incorrect because we can't throw an exception during
	 * the execution of __ToString(), it's a fatal error in PHP
	 *
	 * @param  array  $arguments
	 *
	 * @return GraphQL_Client_Query
	 * @throws GraphQL_Client_Argument_Exception
	 */
	public function set_arguments( array $arguments ): GraphQL_Client_Query {
		// If one of the arguments does not have a name provided, throw an exception
		$non_string_args = array_filter(
			array_keys( $arguments ),
			function ( $element ) {
				return ! is_string( $element );
			}
		);
		if ( ! empty( $non_string_args ) ) {
			throw new GraphQL_Client_Argument_Exception(
				'One or more of the arguments provided for creating the query does not have a key, which represents argument name'
			);
		}

		$this->arguments = $arguments;

		return $this;
	}

	/**
	 * @return string
	 */
	protected function construct_variables(): string {
		if ( empty( $this->variables ) ) {
			return '';
		}

		$vars_string = '(';
		$first       = true;
		foreach ( $this->variables as $variable ) {

			// Append space at the beginning if it's not the first item on the list
			if ( $first ) {
				$first = false;
			} else {
				$vars_string .= ' ';
			}

			// Append variable string value to the variables string
			$vars_string .= $variable;
		}
		$vars_string .= ')';

		return $vars_string;
	}

	/**
	 * @return string
	 */
	protected function construct_arguments(): string { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		// Return empty string if list is empty
		if ( empty( $this->arguments ) ) {
			return '';
		}

		// Construct arguments string if list not empty
		$constraints_string = '(';
		$first              = true;
		foreach ( $this->arguments as $name => $value ) {

			// Append space at the beginning if it's not the first item on the list
			if ( $first ) {
				$first = false;
			} else {
				$constraints_string .= ' ';
			}

			// Convert argument values to graphql string literal equivalent
			if ( is_scalar( $value ) || null === $value ) {
				// Convert scalar value to its literal in graphql
				$value = GraphQL_Client_String_Literal_Formatter::format_value_for_rhs( $value );
			} elseif ( is_array( $value ) ) {
				// Convert PHP arrays to its array representation in graphql arguments
				$value = GraphQL_Client_String_Literal_Formatter::format_array_for_gql_query( $value );
			}
			// TODO: Handle cases where a non-string-convertible object is added to the arguments
			$constraints_string .= $name . ': ' . $value;
		}
		$constraints_string .= ')';

		return $constraints_string;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$query_format         = static::QUERY_FORMAT;
		$selection_set_string = $this->construct_selection_set();

		if ( ! $this->is_nested ) {
			$query_format = $this->generate_signature();
			if ( '' === $this->field_name ) {

				return $query_format . $selection_set_string;
			} else {
				$query_format = $this->generate_signature() . ' {' . PHP_EOL . static::QUERY_FORMAT . PHP_EOL . '}';
			}
		}
		$arguments_string = $this->construct_arguments();

		return sprintf( $query_format, $this->generate_field_name(), $arguments_string, $selection_set_string );
	}

	/**
	 * @return string
	 */
	protected function generate_field_name(): string {
		return empty( $this->alias ) ? $this->field_name : sprintf( '%s: %s', $this->alias, $this->field_name );
	}

	/**
	 * @return string
	 */
	protected function generate_signature(): string {
		$signature_format = '%s%s%s';

		return sprintf( $signature_format, static::OPERATION_TYPE, $this->operation_name, $this->construct_variables() );
	}

	/**
	 *
	 */
	protected function set_as_nested() {
		$this->is_nested = true;
	}
}
