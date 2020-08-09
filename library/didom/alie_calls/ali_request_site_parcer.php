<?php
require_once('bootstrap.php');

use DiDom\Document;

class Ali_Request_Site_Parcer extends Document
{
    
    public $product_values;
    public $ali_product_url;
    
    /*
     *
     * parcer aliexpress site and gets json values of product
     */
    
    public function get_description_url()
    {
        return json_encode($this->product_values['descriptionModule']['descriptionUrl']);
    }
    
    public function get_product_values()
    {
        return json_encode($this->product_values);
    }
    
    public function parcer_ali_product_site()
    {
        
        $attributes_html = $this->find('script');
        
        foreach ($attributes_html as $text) {
            echo "<pre>";
            $val = $this->getProtectedValue($text, 'node');
            //   print_r ($val->nodeValue);
            if (preg_match('#\swindow.runParams\s*=\s*(.*?);\s*$#ms', $val->nodeValue, $matches)) {
                
                $string = explode("data:", $matches[1]);
                $string = explode("csrfToken", $string[1]);
                $x = 0;
                while ($x <= 40) {
                    $string[0] = substr($string[0], 0, -1);
                    
                    $product_values = json_decode($string[0], true);
                    
                    if ($product_values) {
                        break;
                    }
                    $x++;
                }
            }
        }
        $this->product_values = $product_values;
    }
    
    public function getProtectedValue($obj, $name)
    {
        $array = (array)$obj;
        $prefix = chr(0) . '*' . chr(0);
        return $array[$prefix . $name];
    }
    
    public function get_images(){
        
        return json_encode($this->product_values['imageModule']['imagePathList']);
    }
    public function get_variations_attibutes(){
        
        return json_encode($this->product_values['skuModule']);
    }
    
    public function get_specs(){
        
        return json_encode($this->product_values['specsModule']['props']);
    }
    
}

$site_html = new Ali_Request_Site_Parcer('https://aliexpress.ru/item/4001237404951.html', true);

$site_html->parcer_ali_product_site();

//print_r($site_html->get_product_values());
//print_r($site_html->get_images());
//print_r($site_html->get_variations_attibutes());
//print_r($site_html->get_specs());

//echo $des_url = $site_html->get_description_url();

//$desc_site_html = new Ali_Request_Site_Parcer($des_url, true);






