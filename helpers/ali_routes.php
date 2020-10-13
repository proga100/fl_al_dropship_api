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
 * Version:           1.0.0
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
            'get_item_by_id' => '/item_id=(?P<item_id>[a-zA-Z0-9-]+)',
            'get_items' => ''
        ];

        foreach ($bases as $base => $urlquery) {
            register_rest_route($namespace, '/' . $base . $urlquery, array(
                array(
                    'methods' => 'GET',
                    'callback' => array($this, $base),
                    'args' => array(
                        'context' => array(
                            'default' => 'view',
                        ),
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
    public function get_item_by_id($request)
    {
        //get parameters from request
        $params = $request->get_params();

        $item = 'get_item_by_id';//do a query, call another class, etc
        $getitems_query_keys = [
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
        $params ['product_id'] = $params['item_id'];
        $queries_reqs = $this->escape_keys($getitems_query_keys, $params);

        $data = $this->prepare_item_for_response($item, $queries_reqs);

        $data = (array)$data;
        //return a response or error based on some conditional
        if (1 == 1) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('code', __('message', 'text-domain'));
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
        if ($item == 'get_items') {
            $request = apply_filters('fl_api_import_aliexpress_product_search_results', $request);
        } elseif ($item == 'get_item_by_id') {
            $request = apply_filters('fl_api_import_aliexpress_product_search_results', $request);
        }
        return $request;
    }

    public function get_items($request)
    {
        //get parameters from request
        $params = $request->get_params();
        $getitems_query_keys = [
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
        $queries_reqs = $this->escape_keys($getitems_query_keys, $params);

        $item = 'get_items';//do a query, call another class, etc
        $data = $this->prepare_item_for_response($item, $queries_reqs);

        $data = (array)$data;
        //return a response or error based on some conditional
        if (1 == 1) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('code', __('message', 'text-domain'));
        }
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
// TODO add full info of the product and api route for fuul info

		//print_r($site_html->get_product_values());
		//print_r($site_html->get_images());
		//print_r($site_html->get_variations_attibutes());
		//print_r($site_html->get_specs());

		//echo $des_url = $site_html->get_description_url();

		//$desc_site_html = new Ali_Request_Site_Parcer($des_url, true);
	}

}