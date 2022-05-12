<?php

/**
 * Interface QueryBuilderInterface
 *
 * @package GraphQL_Client
 */
interface GraphQL_Client_Query_Builder_Interface {
	/**
	 * @return GraphQL_Client_Query
	 */
	public function get_query(): GraphQL_Client_Query;
}
