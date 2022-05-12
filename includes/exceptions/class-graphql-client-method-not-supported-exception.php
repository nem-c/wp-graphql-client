<?php

/**
 * Class MethodNotSupportedException
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Method_Not_Supported_Exception extends RunTimeException {
	public function __construct( $request_method ) {
		parent::__construct( "Method \"$request_method\" is currently unsupported by client." );
	}
}
