<?php

/**
 * Class NestableObject
 *
 * @package GraphQL_Client
 */
abstract class GraphQL_Client_Nestable_Object {
	// TODO: Remove this method and class entirely, it's purely tech debt
	/**
	 * @return mixed
	 */
	abstract protected function set_as_nested();
}
