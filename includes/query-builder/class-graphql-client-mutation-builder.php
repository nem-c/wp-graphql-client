<?php

class GraphQL_Client_Mutation_Builder extends GraphQL_Client_Query_Builder {
	/**
	 * MutationBuilder constructor.
	 *
	 * @param  string  $query_object
	 * @param  string  $alias
	 */
	public function __construct( string $query_object = '', string $alias = '' ) {
		parent::__construct( $query_object, $alias );
		$this->query = new GraphQL_Client_Mutation( $query_object, $alias );
	}

	/**
	 * Synonymous method to get_query(), it just returns a Mutation type instead of Query type creating a neater
	 * interface when using interfaces
	 *
	 * @return GraphQL_Client_Query
	 */
	public function get_mutation(): GraphQL_Client_Query {
		return $this->get_query();
	}
}
