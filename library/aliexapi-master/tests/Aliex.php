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

    public function testAliexIO($keyword, $pageNo, $pageSize, $sort, $originalPriceFrom, $originalPriceTo, $startCreditScore, $endCreditScore, $currency, $category_id, $language)
    {
        
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
        $comparams['default_hidden'] = 0;
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
        //echo $product_id;
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





