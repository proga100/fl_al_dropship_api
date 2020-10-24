<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.flance.info
 * @since             1.0.0
 * @package           Flance_aliexpress_dropship
 *
 * @wordpress-plugin
 * Plugin Name:       Flance Aliexpress Dropship Api
 * Description:       Creates api for aliexpress.com
 *
 * The component uses  the Aliexpress official providers APIs.
 * Version:           2.0.0
 * Author:            Rusty
 * Author URI:        http://www.flance.info
 * Text Domain:       flance_aliexpress_dropship_api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Ali_Api_Endpoints extends WP_REST_Controller
{

	public function __construct()
	{
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes()
	{
		$version = '1';
		$namespace = 'ali_api_endpoints/v' . $version;

		$bases = [
			'get_item_by_id' => '/product_id=(?P<product_id>[a-zA-Z0-9-]+)',
			'get_items' => '',
			'get_full_info_by_url' => '',
			'get_desc_info_by_url' => ''
		];
		foreach ($bases as $base => $urlquery) {
			register_rest_route($namespace, '/' . $base . $urlquery, array(
				array(
					'methods' => 'GET',
					'callback' => array($this, 'get_base_functions'),
					'args' => array(
						'context' => array(
							'default' => 'view',
						),
						'base' => array(
							'default' => $base,
						)
					),
				)
			));
		}
	}

	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */

	public function get_base_functions($request)
	{

		$params = $request->get_params();
		$base = $params['base'];
		$get_item_by_id = [
			'keyword',
			'affiliate_cat_id',
			'min_price',
			'max_price',
			'min_score',
			'max_score',
			'product_id',
			'currency',
			'language'
		];

		$get_items = [
			'keyword',
			'affiliate_cat_id',
			'min_price',
			'max_price',
			'min_score',
			'max_score',
			'product_id',
			'currency',
			'language',
			'limit',
			'limitstart',
			'directionTable',
			'directionTable'
		];
		$get_full_info_by_url = $getitems_query_keys = [
			'productUrl'
		];
		$get_desc_info_by_url = [
			'ProductDescUrl'
		];;
		$baseVals = [
			'get_item_by_id' => $get_item_by_id,
			'get_items' => $get_items,
			'get_desc_info_by_url' => $get_desc_info_by_url,
			'get_full_info_by_url' => $get_full_info_by_url,
		];
		$getitems_query_keys = $baseVals[$base];

		$queries_reqs = $this->escape_keys($getitems_query_keys, $params);

		$data = $this->prepare_item_for_response($base, $queries_reqs);

		$data = (array)$data;
		//return a response or error based on some conditional

		if (!empty($data['errorCode'])) {
			if ($data['errorCode'] == '20010000') {

				return new WP_REST_Response($data, 200);
			} else {
				return new WP_Error('code', __('message: ', 'text-domain') . $data['errorCode']);
			}
		} else {
			return new WP_Error('code', __('message: ', 'text-domain'));
		}

	}

	public function escape_keys($getitems_query_keys, $params)
	{
		foreach ($getitems_query_keys as $getitems_query_key) {
			$queries_reqs[$getitems_query_key] = (!empty($params[$getitems_query_key])) ? $params[$getitems_query_key] : '';
		}
		return $queries_reqs;
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response($item, $request)
	{
		switch ($item):
			case 'get_items':
				$request = apply_filters('fl_api_import_aliexpress_product_search_results', $request);
				break;
			case 'get_item_by_id':
				$request = apply_filters('fl_api_import_aliexpress_product_search_results', $request);
				$request->result->productUrl = urlencode($request->result->productUrl);
				break;
			case 'get_full_info_by_url':
				$request = $this->get_full_info($request['productUrl']);
				break;
			case 'get_desc_info_by_url':
				$request = $this->get_desc_info($request['ProductDescUrl']);
				break;
		endswitch;
		return $request;
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check($request)
	{
		return $this->get_items_permissions_check($request);
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database($request)
	{
		return array();
	}

	public function get_full_info($ProductUrl)
	{


		$site_html = new Ali_Request_Site_Parcer($ProductUrl, true);

		$site_html->parcer_ali_product_site();


		$data['get_product_values'] = $site_html->get_product_values();
		$data['get_images'] = $site_html->get_images();
		$data['get_variations_attibutes'] = $site_html->get_variations_attibutes();
		$data['get_specs'] = $site_html->get_specs();

		$data['des_url'] = urlencode(trim(stripslashes($site_html->get_description_url()), '"'));

		$data['errorCode'] = '20010000';

		return $data;
	}

	public function get_desc_info($ProductDescUrl)
	{
		$ProductDescUrl = urldecode($ProductDescUrl);

		$data = wp_remote_get($ProductDescUrl);
		$data = (array)$data;

		$data['errorCode'] = ($data['response']['code'] == '200') ? '20010000' : null;

		return $data;
	}

}