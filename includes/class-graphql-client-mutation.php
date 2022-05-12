<?php

/**
 * Class Mutation
 *
 * @package GraphQL
 */
class GraphQL_Client_Mutation extends GraphQL_Client_Query {
	/**
	 * Stores the name of the type of the operation to be executed on the GraphQL server
	 *
	 * @var string
	 */
	protected const OPERATION_TYPE = 'mutation';
}
