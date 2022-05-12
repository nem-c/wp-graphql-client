<?php

interface GraphQL_Client_Auth_Interface {
	/**
	 * @param  Requests  $request
	 * @param  array  $options
	 *
	 * @return Requests
	 */
	public function run( Requests $request, array $options = array() ): Requests;
}
