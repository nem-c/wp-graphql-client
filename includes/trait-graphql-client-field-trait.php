<?php

trait GraphQL_Client_Field_Trait {
	/**
	 * Stores the selection set desired to get from the query, can include nested queries
	 *
	 * @var array
	 */
	protected array $selection_set;

	/**
	 * @param  array  $selection_set
	 *
	 * @return self
	 * @throws GraphQL_Client_Invalid_Selection_Exception
	 */
	public function set_selection_set( array $selection_set ): self {
		$non_strings_fields = array_filter(
			$selection_set,
			function ( $element ) {
				return ! is_string( $element ) && ! $element instanceof GraphQL_Client_Query && ! $element instanceof GraphQL_Client_Inline_Fragment;
			}
		);
		if ( ! empty( $non_strings_fields ) ) {
			throw new GraphQL_Client_Invalid_Selection_Exception(
				'One or more of the selection fields provided is not of type string or Query'
			);
		}

		$this->selection_set = $selection_set;

		return $this;
	}

	/**
	 * @return string
	 */
	protected function construct_selection_set(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		if ( empty( $this->selection_set ) ) {
			return '';
		}

		$attributes_string = ' {' . PHP_EOL;
		$first             = true;
		foreach ( $this->selection_set as $attribute ) {

			// Append empty line at the beginning if it's not the first item on the list
			if ( $first ) {
				$first = false;
			} else {
				$attributes_string .= PHP_EOL;
			}

			// If query is included in attributes set as a nested query
			if ( $attribute instanceof GraphQL_Client_Query ) {
				$attribute->set_as_nested();
			}

			// Append attribute to returned attributes list
			$attributes_string .= $attribute;
		}
		$attributes_string .= PHP_EOL . '}';

		return $attributes_string;
	}

	/**
	 * @return array
	 */
	public function get_selection_set(): array {
		return $this->selection_set;
	}
}
