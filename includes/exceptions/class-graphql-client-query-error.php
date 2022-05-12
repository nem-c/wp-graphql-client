<?php

/**
 * This exception is triggered when the GraphQL endpoint returns an error in the provided query
 *
 * Class QueryError
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Query_Error extends RuntimeException {
	/**
	 * @var array
	 */
	protected array $error_details;

	/**
	 * QueryError constructor.
	 *
	 * @param $error_details
	 */
	public function __construct( $error_details ) {
		$this->error_details = $error_details['errors'][0];
		parent::__construct( $this->error_details['message'] );
	}

	/**
	 * @return array
	 */
	public function get_error_details(): array {
		return $this->error_details;
	}
}
