<?php

/**
 * Class Result
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Results {
	/**
	 * @var string
	 */
	protected string $response_body;

	/**
	 * @var Requests_Response
	 */
	protected Requests_Response $response_object;

	/**
	 * @var array|object
	 */
	protected $results;

	/**
	 * Result constructor.
	 *
	 * Receives json response from GraphQL api response and parses it as associative array or nested object accordingly
	 *
	 * @param  Requests_Response  $response
	 * @param  bool  $as_array
	 *
	 * @throws GraphQL_Client_Query_Error
	 */
	public function __construct( Requests_Response $response, bool $as_array ) {
		$this->response_object = $response;
		$this->response_body   = $this->response_object->body;
		$this->results         = json_decode( $this->response_body, $as_array );

		// Check if any errors exist, and throw exception if they do
		if ( $as_array ) {
			$contains_errors = array_key_exists( 'errors', $this->results );
		} else {
			$contains_errors = isset( $this->results->errors );
		}

		if ( $contains_errors ) {

			// Reformat results to an array and use it to initialize exception object
			$this->reformat_results( true );
			throw new GraphQL_Client_Query_Error( $this->results );
		}
	}

	/**
	 * @param  bool  $as_array
	 */
	public function reformat_results( bool $as_array ): void {
		$this->results = json_decode( $this->response_body, $as_array );
	}

	/**
	 * Returns only parsed data objects in the requested format
	 *
	 * @return array|object
	 */
	public function get_data() {
		if ( is_array( $this->results ) ) {
			return $this->results['data'];
		}

		return $this->results->data;
	}

	/**
	 * Returns entire parsed results in the requested format
	 *
	 * @return array|object
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * @return string
	 */
	public function get_response_body(): string {
		return $this->response_body;
	}

	/**
	 * @return Requests_Response
	 */
	public function get_response_object(): Requests_Response {
		return $this->response_object;
	}
}
