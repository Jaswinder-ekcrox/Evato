<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Brands;


class Syncbrands extends \Evato\Globis\Controller\Adminhtml\Brands
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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$config = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$this->is_enable = $config->getValue('evato_globis/general/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->client_id = $config->getValue('evato_globis/general/client_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->client_secret = $config->getValue('evato_globis/general/client_secret',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_url = $config->getValue('evato_globis/general/api_url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_username = $config->getValue('evato_globis/general/api_username',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->api_password = $config->getValue('evato_globis/general/api_password',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->secret_key = base64_encode($this->api_username.':'.$this->api_password);
		header('Content-Encoding: none;');
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$base_url =	$storeManager->getStore()->getBaseUrl();
		$this->_coreRegistry->register('base_url', $base_url);
		
		// set bearer token
		$this->get_bearer_token();
		// set  Authentication token
		$this->get_access_token();
		$brands_data =  $this->get_brands();
			  //       echo "<pre>";
					// print_r($brands_data);
					// echo "</pre>";
					// die;


		foreach ($brands_data as $key => $brands) {
			        // echo "<pre>";
					// print_r($brands);
					// echo "</pre>";

			     $brand_code = $brands->article_brand_id;
			     $brand_name = $brands->brand_name;
			     $rank = $brands->rank;
			     $hotrunners = $brands->hotrunners;
			     $hotrunners_code = '';
			     foreach ($hotrunners as $key => $value) {
			     	if($key == 0){
			     		$hotrunners_code.= $value->article_id; 
			     	} else{
			     		$hotrunners_code.= ',';
			     		$hotrunners_code.= $value->article_id;
			     	}
			     	     
			     	//$rank = $value->rank;
			     }
			     $html_description = $brands->html_description;
			     foreach ($html_description as $x => $values) {
			     	echo "<pre>";
					print_r($values);
					echo "</pre>";
                        $brand_values = $values->value;
                             
			     }

			      
		}
		
		 //$article; 
		 $brand_name; 
		 $brand_code;
	     $url1 = $brand_name; 
	     $url = strtolower($url1);
		 $date = date("Y-m-d h:i:s"); 
		 $hotrunners_code;
		 $rank;
		 $brand_values;


$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$select_data = $connection->select()->from('ves_brand', array('*'))->where('glob_brand_code="'.$brand_code.'"',1);
$data = $connection->fetchAll($select_data);
$count = count($data);

if ($count > 0) {
$sql1= "UPDATE ves_brand SET name = '$brand_name', glob_brand_code = '$brand_code', url_key = '$url', page_title = '$brand_name', glob_hotrunners = '$hotrunners_code', position = '$rank' WHERE glob_brand_code = '$brand_code'";
$connection->query($sql1);
echo "update";
}else{

$sql = "INSERT INTO ves_brand (name,url_key,description,glob_brand_code,creation_time,update_time,page_title,glob_hotrunners, 	position) VALUES ('$brand_name','$url','$brand_values','$brand_code','$date','$date','$brand_name','$hotrunners_code','$rank')";
$connection->query($sql);
echo "Inserted";
}

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
			echo " bearer not found ";
			echo curl_error($ch);
		} else {
	        $json   = json_decode($result);
			$this->bearer_token = $json->access_token;
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
			echo " access not found  ";
			echo curl_error($ch);
		} else {
	        $json   = json_decode($result);
			$this->access_token = $json->access_token;
		}
	} 
	
	private function get_brands(){
		$url = $this->api_url.$this->client_id.'/article/brands';
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		if($result == False){
			echo " articals images not found  ";
			echo curl_error($ch);
		} else {
	        return json_decode($result);
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
    
