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
        $this->parcer_ali_product_site();
        return $this->product_values['descriptionModule']['descriptionUrl'];
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
    
}

$document = new Ali_Request_Site_Parcer('https://aliexpress.ru/item/4001237404951.html', true);
print_r($document->get_description_url());




