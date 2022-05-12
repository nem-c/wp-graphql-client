<?php

/**
 * Class ArgumentException
 *
 * @package GraphQL_Client
 */
class GraphQL_Client_Client_Exception extends InvalidArgumentException {
	public function get_response(): string {
		return $this->getMessage();
	}
}
