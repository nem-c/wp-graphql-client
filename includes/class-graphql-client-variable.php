<?php

/**
 * Class Variable
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Variable {
	/**
	 * @var string
	 */
	protected string $name;

	/**
	 * @var string
	 */
	protected string $type;

	/**
	 * @var bool
	 */
	protected bool $required;

	/**
	 * @var null|string|int|float|bool
	 */
	protected $default_value;

	/**
	 * Variable constructor.
	 *
	 * @param  string  $name
	 * @param  string  $type
	 * @param  bool  $is_required
	 * @param  null  $default_value
	 */
	public function __construct( string $name, string $type, bool $is_required = false, $default_value = null ) {
		$this->name          = $name;
		$this->type          = $type;
		$this->required      = $is_required;
		$this->default_value = $default_value;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		$var_string = "\$$this->name: $this->type";
		if ( $this->required ) {
			$var_string .= '!';
		} elseif ( ! empty( $this->default_value ) ) {
			$var_string .= '=' . GraphQL_Client_String_Literal_Formatter::format_value_for_rhs( $this->default_value );
		}

		return $var_string;
	}
}
