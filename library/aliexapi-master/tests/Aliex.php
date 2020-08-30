<?php

/*
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
	*/

namespace AliexApi\Tests;

$plugin_current_dir =  plugin_dir_path( __DIR__ );

include_once($plugin_current_dir.'vendor/autoload.php');

use AliexApi\AliexIO;
use AliexApi\Configuration\GenericConfiguration;
use AliexApi\Operations\GetProductDetail;
use AliexApi\Operations\ListProducts;

class AliexIOTest
{
    
    var $comparams = null;
    
    public function setProductId($productId)
    {
        $this->parameter['productId'] = $productId;
        return $this;
    }
    
    public function get_products($res)
    {
        if (!empty($res['directionTable'])) {
            $sort = $res['directionTable'];
            if ($sort == 'asc') $sort = 'orignalPriceUp';
            if ($sort == 'desc') $sort = 'orignalPriceDown';
        } else {
            
            $sort = NUll;
        }
        $res = ['product_id' => '4001237404951'];
        
        $keyword = $res['keyword'];
        
        if (empty($keyword)) {
            $keyword = $_SESSION['keyword'];
        }
        
        $product_id = $res['product_id'];
        foreach ($res as $key => $rt) {
            $_SESSION[$key] = $rt;
        }
        
        if (!empty($res['limitstart'])) {
            $pageNo = $res['limitstart'];
        } else {
            
            $pageNo = 1;
        }
        $currency = $res['vir_currency'];
        
        $endCreditScore = $res['max_score'];
        $startCreditScore = $res['min_score'];
        $originalPriceFrom = $res['min_price'];
        $originalPriceTo = $res['max_price'];
        
        if (!empty($res['limit'])) {
            $pageSize = $res['limit'];
           
        } else {
            
            $res['limit'] = $pageSize = 5;
        }
        
        $category_id = $res['affiliate_cat_id'];
        if (empty($category_id)) $category_id = $_SESSION['affiliate_cat_id'];
        
        //  $comparams['ali_api'] = get_option('aliexpress_key');
        // $comparams['tracking_id'] = get_option('tracking_id');
        
        if ($product_id == '') $aliexpress_json = $this->testAliexIO($keyword, $pageNo, $pageSize, $sort, $originalPriceFrom, $originalPriceTo, $startCreditScore, $endCreditScore, $currency, $category_id);
        if ($product_id != '') $aliexpress_json = $this->testGetProductDetail($product_id, $currency);
        $data = json_decode($aliexpress_json);
        
        print_r($data);
        
        return $data;
    }
    
    public function testAliexIO($keyword, $pageNo, $pageSize, $sort, $originalPriceFrom, $originalPriceTo, $startCreditScore, $endCreditScore, $currency, $category_id)
    {
        $language = get_option('language');
        $currency = get_woocommerce_currency();
        
        $conf = new GenericConfiguration();
        $this->aliconfig($conf);
        $aliexIO = new AliexIO($conf);
        
        $listproducts = new ListProducts();
        $listproducts->setFields('productId,productTitle,productUrl,imageUrl,allImageUrls,localPrice,salePrice,discount,evaluateScore,originalPrice,commission,commissionRate,30daysCommission,volume,packageType,lotNum,validTime');
        
        $listproducts->setLanguage($language);
        $listproducts->setLocalCurrency($currency);
        $listproducts->setSort($sort);
        $listproducts->setPageNo($pageNo);
        $listproducts->setPageSize($pageSize);
        $listproducts->setKeywords($keyword);
        $listproducts->setOriginalPriceFrom($originalPriceFrom);
        $listproducts->setOriginalPriceTo($originalPriceTo);
        $listproducts->setStartCreditScore($startCreditScore);
        $listproducts->setEndCreditScore($endCreditScore);
        $listproducts->setCategoryId($category_id);
        $listproducts->setHighQualityItems('true');
        $formattedResponse = $aliexIO->runOperation($listproducts);
        
        return $formattedResponse;
    }
    
    public function aliconfig($conf)
    {
        
        $comparams = $this->comparams;
        
        $this->default_hidden = $comparams['default_hidden'];
        
        $this->ali_api = $comparams['ali_api'];
        $this->tracking_id = $comparams['tracking_id'];
        
        if ($comparams['default_hidden'] == 1) {
            
            $conf
                ->setApiKey($comparams['ali_api'])
                ->setTrackingKey($comparams['tracking_id'])
                ->setDigitalSign('dummydigitalsign');
            return $conf;
        } else {
            
            $conf
                ->setApiKey('12345')
                ->setTrackingKey('trackkey')
                ->setDigitalSign('dummydigitalsign');
            return $conf;
        }
    }
    
    public function testGetProductDetail($product_id, $currency)
    {
        $currency = 'US';
        $language = 'EN';
        
        // $currency = get_option('currency');
        
        $conf = new GenericConfiguration();
        $this->aliconfig($conf);
        //  print_r ($conf);
        $aliexIO = new AliexIO($conf);
        
        $listproductdetails = new GetProductDetail();
        $listproductdetails->setFields('productId,productTitle,productUrl,imageUrl,allImageUrls,localPrice,salePrice,discount,evaluateScore,originalPrice,commission,commissionRate,30daysCommission,volume,packageType,lotNum,validTime');
        // $listproductdetails->setFields('productId,productTitle,productUrl,imageUrl,localPrice,salePrice,discount,evaluateScore,originalPrice');
        
        $listproductdetails->setProductId($product_id);
        $listproductdetails->setLocalCurrency($currency);
        $listproductdetails->setLanguage($language);
        
        $formattedResponse = $aliexIO->runOperation($listproductdetails);
        
        return $formattedResponse;
    }
    
}





