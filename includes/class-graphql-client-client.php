<?php

/**
 * Class Client
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Client {
	/**
	 * @var string
	 */
	protected string $endpoint_url;

	/**
	 * @var array
	 */
	protected array $http_headers;

	/**
	 * @var array
	 */
	protected array $options;

	/**
	 * @var string
	 */
	protected string $request_method;

	/**
	 * @var GraphQL_Client_Auth_Interface
	 */
	protected GraphQL_Client_Auth_Interface $auth;

	/**
	 * Client constructor.
	 *
	 * @param  string  $endpoint_url
	 * @param  array  $authorization_headers
	 * @param  array  $http_options
	 * @param  string  $request_method
	 * @param  GraphQL_Client_Auth_Interface|null  $auth
	 */
	public function __construct( string $endpoint_url, array $authorization_headers = array(), array $http_options = array(), string $request_method = 'POST', GraphQL_Client_Auth_Interface $auth = null
	) {
		$headers = array_merge(
			$authorization_headers,
			$http_options['headers'] ?? array(),
			array( 'Content-Type' => 'application/json' )
		);
		/**
		 * All headers will be set on the request objects explicitly,
		 * Guzzle doesn't have to care about them at this point, so to avoid any conflicts
		 * we are removing the headers from the options
		 */
		unset( $http_options['headers'] );
		$this->options = $http_options;
		if ( $auth ) {
			$this->auth = $auth;
		}

		$this->endpoint_url = $endpoint_url;
		$this->http_headers = $headers;
		if ( 'POST' !== $request_method ) {
			throw new GraphQL_Client_Method_Not_Supported_Exception( $request_method );
		}
		$this->request_method = $request_method;
	}

	/**
	 * @param  GraphQL_Client_Query|GraphQL_Client_Query_Builder_Interface  $query
	 * @param  bool  $results_as_array
	 * @param  array  $variables
	 *
	 * @return GraphQL_Client_Results
	 * @throws Requests_Exception
	 */
	public function run_query( $query, bool $results_as_array = false, array $variables = array() ): GraphQL_Client_Results {
		if ( $query instanceof GraphQL_Client_Query_Builder_Interface ) {
			$query = $query->get_query();
		}

		if ( ! $query instanceof GraphQL_Client_Query ) {
			throw new TypeError( 'GraphQL_Client_Client::run_query accepts the first argument of type GraphQL_Client_Query or GraphQL_Client_Query_Builder_Interface' );
		}

		return $this->run_raw_query( (string) $query, $results_as_array, $variables );
	}

	/**
	 * @param  string  $query_string
	 * @param  bool  $results_as_array
	 * @param  array  $variables
	 *
	 * @return GraphQL_Client_Results
	 * @throws Requests_Exception
	 */
	public function run_raw_query( string $query_string, bool $results_as_array, array $variables = array() ): GraphQL_Client_Results { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.CyclomaticComplexity.TooHigh
		// Convert empty variables array to empty json object
		if ( empty( $variables ) ) {
			$variables = (object) null;
		}
		// Set query in the request body
		$body_array = array(
			'query'     => $query_string,
			'variables' => $variables,
		);

		// Send api request and get response
		try {
			$response = Requests::request(
				$this->endpoint_url,
				$this->http_headers,
				$body_array,
				$this->request_method,
				$this->options
			);
		} catch ( Requests_Exception $exception ) {
			$status_code = $exception->getCode();
			$response    = new Requests_Response();

			// If exception thrown by client is "400 Bad Request ", then it can be treated as a successful API request
			// with a syntax error in the query, otherwise the exceptions will be propagated
			if ( 400 !== $status_code ) {
				throw $exception;
			}
		}

		// Parse response to extract results
		return new GraphQL_Client_Results( $response, $results_as_array );
	}
}
