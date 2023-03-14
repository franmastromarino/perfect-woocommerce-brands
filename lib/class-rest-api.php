<?php

namespace QuadLayers\PWB;

use \WP_Error, WP_REST_Server, \WC_REST_Terms_Controller;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class Rest_Api extends WC_REST_Terms_Controller {

	private $namespaces = array( 'wc/v1', 'wc/v2', 'wc/v3' );
	protected $base     = 'brands';
	protected $taxonomy = 'pwb-brand';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
		add_action( 'rest_api_init', array( $this, 'register_fields' ) );

		add_filter( "rest_{$this->taxonomy}_collection_params", array( $this, 'modify_collection_params' ), 10, 2 );
		add_filter( 'woocommerce_rest_product_object_query', array( $this, 'brand_query_args' ), 10, 2 );

	}

	/**
	 * Registers the endpoint for all possible $namespaces
	 */
	public function register_endpoints() {
		foreach ( $this->namespaces as $namespace ) {
			register_rest_route(
				$namespace,
				$this->base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_item' ),
						'permission_callback' => array( $this, 'create_item_permissions_check' ),
						'args'                => array_merge(
							$this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
							array(
								'name' => array(
									'description' => __( 'Brand name.', 'perfect-woocommerce-brands' ),
									'required'    => true,
									'type'        => 'string',
								),
							)
						),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);

			register_rest_route(
				$namespace,
				$this->base . '/(?P<id>[\d]+)',
				array(
					'args'   => array(
						'id' => array(
							'description' => __( 'Unique identifier for the resource.', 'perfect-woocommerce-brands' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => array(
							'context' => $this->get_context_param( array( 'default' => 'view' ) ),
						),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'delete_item_permissions_check' ),
						'args'                => array(
							'force' => array(
								'default'     => true,
								'type'        => 'boolean',
								'description' => __( 'Whether to bypass trash and force deletion.', 'perfect-woocommerce-brands' ),
							),
						),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);

			register_rest_route(
				$namespace,
				$this->base . '/batch',
				array(
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'batch_items' ),
						'permission_callback' => array( $this, 'batch_items_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
					'schema' => array( $this, 'get_public_batch_schema' ),
				)
			);
		}
	}


	public function modify_collection_params( $query_params, $taxonomy ) {
		unset( $query_params['post'] );
		unset( $query_params['per_page'] );

		$query_params['product'] = array(
			'description' => __( 'Limit result set to terms assigned to a specific product.' ),
			'type'        => 'integer',
			'default'     => null,
		);

		$query_params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set.' ),
			'type'              => 'integer',
			'default'           => 1000,
			'minimum'           => 1,
			'maximum'           => 1000,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}


	/**
	 * Prepare a single brand output for response.
	 *
	 * @param WP_Term         $item    Term object.
	 * @param WP_REST_Request $request Request instance.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'term_id'          => (int) $item->term_id,
			'name'             => $item->name,
			'slug'             => $item->slug,
			'term_group'       => $item->term_group,
			'term_taxonomy_id' => $item->term_taxonomy_id,
			'taxonomy'         => $this->taxonomy,
			'description'      => $item->description,
			'parent'           => $item->parent,
			'count'            => (int) $item->count,
			'filter'           => $item->filter,
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		/**
		 * Filter a term item returned from the API.
		 *
		 * Allows modification of the term data right before it is returned.
		 *
		 * @param WP_REST_Response  $response  The response object.
		 * @param object            $item      The original term object.
		 * @param WP_REST_Request   $request   Request used to generate the response.
		 * @since 2.3.0
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->taxonomy}", $response, $item, $request );
	}

	/**
	 * Add image fields to term item returned form the API
	 */
	public function add_additional_fields_to_object( $item, $request ) {

		$brand_image_id       = get_term_meta( $item['term_id'], 'pwb_brand_image', true );
		$brand_banner_id      = get_term_meta( $item['term_id'], 'pwb_brand_banner', true );
		$item['brand_image']  = wp_get_attachment_image_src( $brand_image_id );
		$item['brand_banner'] = wp_get_attachment_image_src( $brand_banner_id );

		return $item;

	}

	/**
	 * Entry point for all rest field settings
	 */
	public function register_fields() {
		register_rest_field(
			'product',
			'brands',
			array(
				'get_callback'    => array( $this, 'get_callback' ),
				'update_callback' => array( $this, 'update_callback' ),
				'schema'          => $this->get_schema(),
			)
		);
	}

	/**
	 * Returns the schema of the "brands" field on the /product route
	 * To attach a brand to a product just append a "brands" key containing an array of brand id's
	 * An empty array wil detach all brands.
	 *
	 * @return array
	 */
	public function get_schema() {

		return array(
			'description' => __( 'Product brands.', 'perfect-woocommerce-brands' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'context'     => array( 'view', 'edit' ),
		);
	}

	/**
	 * Returns all attached brands to a GET request to /products(/id)
	 *
	 * @param $product
	 * @return array|\WP_Error
	 */
	public function get_callback( $product ) {
		$brands = wp_get_post_terms( $product['id'], 'pwb-brand' );

		$result_brands_array = array();
		foreach ( $brands as $brand ) {
			$result_brands_array[] = array(
				'id'   => $brand->term_id,
				'name' => $brand->name,
				'slug' => $brand->slug,
			);
		}

		return $result_brands_array;
	}

	/**
	 * Entry point for an update call
	 *
	 * @param $brands
	 * @param $product
	 */
	public function update_callback( $brands, $product ) {
		$this->remove_brands( $product );
		$this->add_brands( $brands, $product );
	}


	/**
	 * Detaches all brands from a product
	 *
	 * @param \WC_Product $product
	 */
	private function remove_brands( $product ) {
		$brands = wp_get_post_terms( $product->get_id(), 'pwb-brand' );
		if ( ! empty( $brands ) ) {
			wp_set_post_terms( $product->get_id(), array(), 'pwb-brand' );
		}
	}

	/**
	 * Attaches the given brands to a product. Earlier attached brands, not in this array, will be removed
	 *
	 * @param array       $brands
	 * @param \WC_Product $product
	 */
	private function add_brands( $brands, $product ) {
		wp_set_post_terms( $product->get_id(), $brands, 'pwb-brand' );
	}

	/**
	 * Add brands to product query args
	 *
	 * @param array $args
	 * @param array $request
	 */
	public function brand_query_args( $args, $request ) {
		// Filter product type by slug.
		$tax_query = array();

		// Filter product type by slug.
		if ( ! empty( $request['brand'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $this->taxonomy,
				'field'    => 'slug',
				'terms'    => $request['brand'],
			);
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		return $args;
	}
}
