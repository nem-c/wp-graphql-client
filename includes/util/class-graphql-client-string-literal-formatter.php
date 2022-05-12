<?php

/**
 * Class StringLiteralFormatter
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_String_Literal_Formatter {
	/**
	 * Converts the value provided to the equivalent RHS value to be put in a file declaration
	 *
	 * @param  string|int|float|bool  $value
	 *
	 * @return string
	 */
	public static function format_value_for_rhs( $value ): string { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded
		if ( is_string( $value ) ) {
			if ( ! static::is_variable( $value ) ) {
				$value = str_replace( '"', '\"', $value );
				if ( strpos( $value, "\n" ) !== false ) {
					$value = '"""' . $value . '"""';
				} else {
					$value = "\"$value\"";
				}
			}
		} elseif ( is_bool( $value ) ) {
			if ( $value ) {
				$value = 'true';
			} else {
				$value = 'false';
			}
		} elseif ( null === $value ) {
			$value = 'null';
		} else {
			$value = (string) $value;
		}

		return $value;
	}

	/**
	 * Treat string value as variable if it matches variable regex
	 *
	 * @param  string  $value
	 *
	 * @return bool
	 */
	private static function is_variable( string $value ): bool {
		return preg_match( '/^\$[_A-Za-z][_0-9A-Za-z]*$/', $value );
	}

	/**
	 * @param  array  $array
	 *
	 * @return string
	 */
	public static function format_array_for_gql_query( array $array ): string {
		$arr_string = '[';
		$first      = true;
		foreach ( $array as $element ) {
			if ( $first ) {
				$first = false;
			} else {
				$arr_string .= ', ';
			}
			$arr_string .= self::format_value_for_rhs( $element );
		}
		$arr_string .= ']';

		return $arr_string;
	}

	/**
	 * @param  string  $string_value
	 *
	 * @return string
	 */
	public static function format_upper_camel_case( string $string_value ): string {
		if ( strpos( $string_value, '_' ) === false ) {
			return ucfirst( $string_value );
		}

		return str_replace( '_', '', ucwords( $string_value, '_' ) );
	}

	/**
	 * @param  string  $string_value
	 *
	 * @return string
	 */
	public static function format_lower_camel_case( string $string_value ): string {
		return lcfirst( self::format_upper_camel_case( $string_value ) );
	}
}
