<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Products;

class Syncarticles extends \Evato\Globis\Controller\Adminhtml\Products
{
	protected $is_enable;
	protected $client_id;
	protected $client_secret;
	protected $api_url;
	protected $api_username;
	protected $api_password;
	protected $bearer_token;
	protected $access_token;
	protected $secret_key;
     
public function execute()
    {
		header('Content-Encoding: none;');
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$base_url =	$storeManager->getStore()->getBaseUrl();
		$this->_coreRegistry->register('base_url', $base_url);
		
		//echo '<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css" rel="stylesheet" />';
		echo '<link  rel="stylesheet" type="text/css"  media="all" href="'.$base_url.'/pub/static/adminhtml/Magento/backend/en_US/Evato_Globis/css/custom.css" />';
		echo '<div class="container">';
		echo '<div class="header-main" style="background:url('.$base_url.'/custom_images/bg.jpg);">
				  <div class="logo"><a href="#"><img src="'.$base_url.'/custom_images/logo.png" alt="" /></a></div>
				  <div class="header-right">
				  <div class="serch"><a href="#"><i class="fa fa-search" aria-hidden="true"></i></a></div>
					<span>Nieuwe<br />
					modules</span>
				  </div>
			  </div>';
		echo '<div class="header-btm">
				<p>Product Synchronisatie ERP naar Magento2 (import/update)</p>
			  </div>';
		flush();
		
		$config = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$this->is_enable = $config->getValue('evato_globis/general/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->client_id = $config->getValue('evato_globis/general/client_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->client_secret = $config->getValue('evato_globis/general/client_secret',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_url = $config->getValue('evato_globis/general/api_url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_username = $config->getValue('evato_globis/general/api_username',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_password = $config->getValue('evato_globis/general/api_password',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->secret_key = base64_encode($this->api_username.':'.$this->api_password);
		
		$image = $base_url.'/custom_images/pbar-ani.gif';
			
		echo '<div class="sage-main">';
		echo '<div class="sges">';
		echo '<div class="sage"><a href="#"><img src="'.$base_url.'/custom_images/globis.jpg" alt="" /></a></div>';
		echo '<div class="progess">';
		echo '<div id="percent" style="width"></div>
		<div id="progress" style="width:500px;border:1px solid #000;"></div>
		<div id="information" style="width"></div>
		<div id="completed" style="width"></div>';
		echo '</div>';
		echo '<div class="magento"><a href="#"><img src="'.$base_url.'/custom_images/magento.jpg" alt="" /></a></div>';
		echo '</div>';
		flush();
		echo '<div class="resault">';
		echo '<h3>Resultaat</h3>';
		echo '<div class="resault-tble">';
		echo '<ul>';
		flush();
		
		// set bearer token
		$this->get_bearer_token();
		
		// set  Authentication token
		$this->get_access_token();
		
		// get Article generics
		$articals_data =  $this->get_articals();
		$total = count($articals_data);
		$m = 1;
		
		if($total > 0){
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> Start synchronisatie products in magento.</li>';
		} else{
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> No Products to import.</li>';
		}
		echo '</ul>';
		flush();
		
		echo '<table cellpadding="0" cellspacing="0" width="100%">';
		echo '<tr style="background:#514943; color:#fff"><td>Artcode</td><td>Type</td><td>SKU</td><td>Resultaat</td></tr>';
	 
	if(count($articals_data) > 0){
		foreach($articals_data as $x=>$articals){

			// echo "<pre>";
			// print_r($articals);
			// echo "</pre>";
			// die;

		    $new_from = $articals->new_from;  
			$seo_keywords = $articals->seo_keywords;
			$seo_short_description = $articals->seo_short_description;
			$net_weight_in_kg = $articals->net_weight_in_kg; 

		    $ean_code = $articals->ean_code; 
			$new_until = $articals->new_until;
			$glob_featured_until = $articals->featured_until;
			$allow_in_webshop = $articals->allow_in_webshop; 
		    $article_code = $articals->article_code;  
		    $featured_from = $articals->featured_from;
		   
			//$articletype_code = '';
		    $article_id = $articals->article_id; 

           // $articlespecifics_data = $this->get_articlspecifics($article_id); 

           $articalspecifics_data =  $this->get_articlspecifics($article_id);
		   if(count($articalspecifics_data) > 0){
		  
				$stockarr = $articalspecifics_data[0]->stock;
				// echo "<pre>";
				// print_r($stockarr);
				// echo "</pre>";
				// die;

				$available_stock =$stockarr->available_stock; 
				$expected_delivery_date =$stockarr->expected_delivery_date; 
				$max_delivery_in_days =$stockarr->max_delivery_in_days;
				$min_delivery_in_days =$stockarr->min_delivery_in_days; 

				$pricesarr = $articalspecifics_data[0]->prices;
				if(count($pricesarr) > 0){
					$net_price =$pricesarr[0]->net_price; 
				}
			}

			 $articlestatus_id = $articals->articlestatus_id; 
			 if($articlestatus_id != ''){
			 	$articlestatus_data =  $this->get_articlestatus($articlestatus_id);
			 	$articlestatus_code = $articlestatus_data[0]->articlestatus_code;
			    $allow_in_webshop = $articlestatus_data[0]->allow_in_webshop; 

			    } else{
			 	$articlestatus_code = '';
			 }

			 $articletype_id = $articals->articletype_id;
             if($articletype_id != ''){
			 	$articletype_data =  $this->get_articltypes($articletype_id);
			 	$articletype_code = $articletype_data[0]->articletype_code;
			 	$allow_in_webshop = $articletype_data[0]->allow_in_webshop;
			   }else{
			 	$articletype_code = '';
			   }

			   $articlesubgroup_id = $articals->articlesubgroup_id; 
               $images_arr = $articals->images; 
               foreach($images_arr as $in=>$images_data){
			   $articleimage_id = $images_data->articleimage_id; 
			   //echo $articleimage_id;
			   $articleimages_data = $this->get_articlimages($articleimage_id); 
			   if(count($articleimages_data) > 0)
			  	{
					 // echo "<pre>";
					 // print_r($articleimages_data);
					 // echo "</pre>";
					 // die;
							
					$image_data = $articleimages_data[0];
					if($image_data){
						$data = 'data:image/jpeg;base64,'.$image_data; // image data
						$dir_path = 'pub/media/import/'; 
						//echo '<img src="data:image/png;base64,'.$data.'" />'; 
						list($type, $data) = explode(';', $data);
						list(, $data)      = explode(',', $data);			
						$data = base64_decode($data);
						$img_name = 'image_'.$article_code;
						file_put_contents($dir_path.$img_name.".jpeg", $data);
						$saved_image = $dir_path.$img_name.".jpeg";
						$real_path = $_SERVER["DOCUMENT_ROOT"].'/'.$saved_image; 
					} else{
						$real_path = '';
					}     

				} else{
					$real_path = '';
				}
     }           

    
               
			$minimum_orderquantity = $articals->minimum_orderquantity;
			//$article_code = $articals->article_code;
			$quantity_multiple_of = $articals->quantity_multiple_of;
		    $sku = $articals->article_code; 
			
			$properties_arr = $articals->properties;
			
			/************Custom Attributes For Website *********************/
			 
			 foreach($properties_arr as $properties_data){

				    if($properties_data->attribute_name == 'MAGNETRON') {
						$magnetron = $properties_data->attribute_value; 
					}
				    if($properties_data->attribute_name == 'OVEN'){
				    	 $oven = $properties_data->attribute_value; 
				    }
				    if($properties_data->attribute_name == 'GEZIEN_OP_AMUZE'){
				    	 $gezien_op_amuze = $properties_data->attribute_value; 
				    }
				    if($properties_data->attribute_name == 'NIEUW'){
				    	 $nieuw = $properties_data->attribute_value;
				    }
				    if($properties_data->attribute_name == 'ONZE_KEUZE'){
				    	 $onze_keuze = $properties_data->attribute_value;
				    }
				    if($properties_data->attribute_name == 'DIEPVRIES'){
				    	 $diepvries = $properties_data->attribute_value;
				    }
				    if($properties_data->attribute_name == 'PROMO'){
				    	 $promo = $properties_data->attribute_value; 
				    }

			} 
			
			
            $web_attr_arr = array('pv_magentron'=>$magnetron,'pv_oven'=>$oven,'pv_gezien_op_amuze'=>$gezien_op_amuze,'pv_nieuw'=>$nieuw,'pv_onze_keuze'=>$onze_keuze,'pv_diepvries'=>$diepvries,'pv_promo'=>$promo);

			 echo "<pre>";
			 print_r($web_attr_arr);
			 echo "</pre>";
					
		/*************end of custom attributes codes***************/

			$related_arr = $articals->related;
			$short_description_arr = $articals->short_description;
			$description_arr = $articals->description;
			$extended_description_arr = $articals->extended_description;
		    $images_arr = $articals->images; 
		    $sub_group_code = '';
		   
			foreach($description_arr as $in=>$description_data){
				if($description_data->language_code == 'NL'){
					 $product_title = $description_data->value; 

				}
			}
 
			$extr_attr_arr = array('glob_article_status_id'=>$articlestatus_id,'glob_sub_group_id'=>$articlesubgroup_id,'glob_article_type_id'=>$articletype_id,'glob_article_code'=>$article_id,'glob_article_status_code'=>$articlestatus_code,'glob_article_type_code'=>$articletype_code,'glob_expected_delivery_date '=>$expected_delivery_date,'glob_min_delivery_in_days '=>$min_delivery_in_days,'glob_max_delivery_in_days '=>$max_delivery_in_days,'glob_ean_code'=>$ean_code,'glob_featured_from'=>$featured_from,'glob_featured_until'=>$glob_featured_until,'glob_minimum_order_quantity'=>$minimum_orderquantity,'glob_new_from'=>$new_from,'glob_new_until'=>$new_until,'glob_quantity_multiple_of'=>$quantity_multiple_of,'glob_sub_group_code'=>$sub_group_code,'glob_allow_in_webshop'=>$allow_in_webshop );

			$product = $objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku',$sku);
			if($product){
				$this->update_product($sku,$product_title,$article_code,$articletype_code,$articlesubgroup_id,$minimum_orderquantity,$quantity_multiple_of,$extr_attr_arr,$articleimage_id,$real_path,$article_id,$pricesarr,$stockarr,$net_price,$available_stock,$web_attr_arr);
				echo '<tr>';
				echo '<td>'.$sku.'</td>';
				echo '<td>Update</td>';
				echo '<td>'.$article_code.'</td>';
				echo '<td>Geupdate</td>';
				echo '</tr>';
			} else{
				$this->insert_product($sku,$product_title,$article_code,$articletype_code,$articlesubgroup_id,$minimum_orderquantity,$quantity_multiple_of,$extr_attr_arr,$articleimage_id,$real_path,$article_id,$pricesarr,$stockarr,$net_price,$available_stock,$web_attr_arr);
				echo '<tr>';
				echo '<td>'.$sku.'</td>';
				echo '<td>Insert</td>';
				echo '<td>'.$article_code.'</td>';
				echo '<td>Creatie</td>';
				echo '</tr>';
			}
			/***************Code for showing the Progress Bar***********************/
				
				$percent = intval($m/$total * 100)."%";
				 echo '<script language="javascript">
				document.getElementById("percent").innerHTML="'.$percent.'";
				document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-image:url('.$image.');\">&nbsp;</div>";
				document.getElementById("information").innerHTML="'.$m.' van '.$total.' artikel(en) gesynchroniseerd.";
				</script>';
				echo str_repeat(' ',1024*64);
				flush();
				//sleep(1);
						
			/*******************************************************************/
			$m++;	
		}
	}
	
	if($total == 0){
		echo '<script language="javascript">document.getElementById("completed").innerHTML="Geen Records Gevonden"</script>';
	} else{
		echo '<script language="javascript">document.getElementById("completed").innerHTML="Process completed"</script>';
	}
	echo '</table>';
	echo '<ul>';
	$this->refreshCache();
	echo '</ul>';
	flush();
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
		
}
	

    private function get_bearer_token(){
		$url = $this->api_url.$this->client_id.'/oauth/token'; 
		$headers[] = 'Authorization:'.$this->client_secret;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$result = curl_exec($ch);
		if($result == False){
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> Bearer token not found (/oauth/token)</li>';
			die;
			//echo curl_error($ch);
		} else {
	        $json   = json_decode($result);
			if($json == ''){
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Bearer token not found. Please check API fucntion (/oauth/token)</li>';
				die;
			} else{
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Call naar bearer token (/oauth/token)</li>';
				$this->bearer_token = $json->access_token;
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Bearer Token = '.$json->access_token.'</li>';
				flush();
			}
			
		}
	}  
	 private function get_access_token(){
	    $url = $this->api_url.$this->client_id.'/oauth/authenticate'; 
		$headers[] = 'Authorization:'.$this->bearer_token;
		$headers[] = 'Content-Type: Application/json';
		$headers[] = 'Credentials:'.$this->secret_key;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		if($result == False){
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> Access token not found (/oauth/authenticate)</li>';
			die;
			//echo curl_error($ch);
		} else {
	        $json   = json_decode($result);
			if($json == ''){
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Access token not found. Please check API fucntion (/oauth/authenticate)</li>';
				die;
			} else{
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Call naar access token (/oauth/authenticate)</li>';
				$this->access_token = $json->access_token;
				echo '<li><i class="fa fa-check" aria-hidden="true"></i> Access Token = '.$json->access_token.'</li>';
				flush();
			}
			
		}
	} 
	private function get_articals(){
		$url = $this->api_url.$this->client_id.'/article/articlegenerics?limit=10&offset=200'; 
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		if($result == False){
			//echo " articals articlegenerics not found  ";
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> articlegenerics not found  (/article/articlegenerics)</li>';
			die;
			//echo curl_error($ch);
		} else {
			echo '<li><i class="fa fa-check" aria-hidden="true"></i> Call naar articlegenerics (/article/articlegenerics)</li>';
	        return json_decode($result);
			flush();
		}
	}
	private function get_articlestatus($articlestatus_id){
	    $url = $this->api_url.$this->client_id.'/article/statuses/'.$articlestatus_id.''; 
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 $result = curl_exec($ch); 
		if($result == False){
			echo " articals status not found  ";
			echo curl_error($ch);
		} else {
	        return json_decode($result);
		}
	}
    private function get_articltypes($articletype_id){
	 	 $url = $this->api_url.$this->client_id.'/article/types?articletype_id='.$articletype_id.''; 
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 $result = curl_exec($ch); 
		if($result == False){
			echo " articals types not found  ";
			echo curl_error($ch);
		} else {
	        return json_decode($result);
		}
	}

	private function get_articlimages($articleimage_id){
		    $url = $this->api_url.$this->client_id.'/article/images/'.$articleimage_id; 
			$headers[] = 'Authorization:'.$this->access_token;
			$headers[] = 'Content-Type: Application/json';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $result = curl_exec($ch); 
			if($result == False){
				echo " image not found  ";
				echo curl_error($ch);
			} else {
		        return json_decode($result);
			}
		}

		private function get_articlspecifics($article_id){
		    $url = $this->api_url.$this->client_id.'/article/articlespecifics/'.$article_id; 
			$headers[] = 'Authorization:'.$this->access_token;
			$headers[] = 'Content-Type: Application/json';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $result = curl_exec($ch); 
			if($result == False){
				echo " image not found  ";
				echo curl_error($ch);
			} else {
		        return json_decode($result);
			}
		}


	// private function get_articlstructures(){
	//  	 $url = $this->api_url.$this->client_id.'/article/structures?attribute_id='; 
	// 	$headers[] = 'Authorization:'.$this->access_token;
	// 	$headers[] = 'Content-Type: Application/json';
	// 	$ch = curl_init();
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 	curl_setopt($ch, CURLOPT_URL, $url);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	 $result = curl_exec($ch); 
	// 	if($result == False){
	// 		echo " articals types not found  ";
	// 		echo curl_error($ch);
	// 	} else {
	//         return json_decode($result);
	// 	}
	// }

	
	public function insert_product($sku,$product_title,$article_code,$articletype_code,$articlesubgroup_id,$minimum_orderquantity,$quantity_multiple_of,$extr_attr_arr,$articleimage_id,$real_path,$article_id,$pricesarr,$stockarr,$net_price,$available_stock,$web_attr_arr){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
		$product = $objectManager->create('\Magento\Catalog\Model\Product');
		$product->setSku($sku); // Set your sku here
		$product->setName($product_title); // Name of Product
		$product->setAttributeSetId(4); // Attribute set id
		$product->setStatus(1); // Status on product enabled/ disabled 1/0
		$product->setWeight(10); // weight of product
		$product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
		$product->setTaxClassId(0); // Tax class id
		$product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)

        //foreach ($pricesarr as $key => $value) {
         //$net_price =$pricesarr[0]->net_price; 
		 $product->setPrice($net_price);
	    //}


		//$product->setPrice(100); // price of product
		 //foreach ($stockarr as $qu => $quantity) {
        // $available_stock =$stockarr->available_stock;
			 //$product->setStockData($available_stock);
		 $product->setStockData(array(
				'use_config_manage_stock' => 0,
				'manage_stock' => 1,
				'is_in_stock' => 1,
				'qty' => $available_stock,
				)
			);
//}
		$product->save();

          


		$_product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);
		foreach ($extr_attr_arr as $x => $y) {
	    $_product->setData($x,$y);
		}
		//$_product->save();


		//$_product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);
		foreach ($web_attr_arr as $code => $val) {
					
	   	 $_product->setData($code,$val);
		}
	    $_product->save();


		 $product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);
		 if ( file_exists($real_path) ) {
		 $imagePath = $real_path; // path of the image
		 //$product->setMediaGallery (array('images'=>array (), 'values'=>array ())); 
		 $product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), true, true);
		 $product->save();
         }else{
	     echo "insert error";
}

    }
	
	public function update_product($sku,$product_title,$article_code,$articletype_code,$articlesubgroup_id,$minimum_orderquantity,$quantity_multiple_of,$extr_attr_arr,$articleimage_id,$real_path,$article_id,$pricesarr,$stockarr,$net_price,$available_stock,$web_attr_arr){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);		
		$product_id = $product->getEntityId();
		
		if($product){
			$product->setSku($sku);
			$product->setName($product_title);
			//$product->setDescription($long_description);
			//$product->setShortDescription($short_description);
			// $product->setData('is_in_stock', 1);
			// $product->setData('qty', 5);
			// $product->setData('manage_stock', 1);

			$product->setStatus(1);
			$product->setWeight(10);
			//$product->setMetaTitle($meta_title);
			//$product->setMetaKeyword($meta_keyword);
			//$product->setMetaDescription($meta_description);
			$product->setTaxClassId(0); 
			$product->setUrlKey(false);
			$product->setVisibility(4);
			//$product->setAttributeSetId(4);
			//$product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
			$product->setPrice($net_price);
		    $product->save();
			//$product->setWebsiteIds(array(1));
			//$product->setCategoryIds(array(2));
   			$product->setStockData(array(
					'use_config_manage_stock' => 0,
					'manage_stock' => 1,
					'is_in_stock' => 1,
					'qty' => $available_stock,
					)
				);
		$product->save();

	    foreach ($extr_attr_arr as $x => $y) {

			    $product->setData($x,$y);
		}
			$product->save();

	 $product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);
		
		foreach ($web_attr_arr as $code => $val) {
					
	   	 $product->setData($code,$val);
		}
	    $product->save();

		


 $product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->get($sku);
   if ( file_exists($real_path) ) {
                
		 $imagePath = $real_path; // path of the image
		 //$product->setMediaGallery (array('images'=>array (), 'values'=>array ())); 
		 $product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), true, true);
		 $product->save();
 
            }else{
	echo "Update error";
}
		}
	}
      
	public function refreshCache(){
		$command = 'php bin/magento cache:clean && php bin/magento cache:flush';
		shell_exec($command);	
		echo '<li><i class="fa fa-check" aria-hidden="true"></i>';
		echo 'Cache Cleaned Successfully';
		echo '</li>';
	}  
    }
    
