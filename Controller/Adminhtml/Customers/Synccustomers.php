<?php
/**
 * Copyright © 2015 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Customers;

class Synccustomers extends \Evato\Globis\Controller\Adminhtml\Customers
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

		//$this->get_relations();

         $relations_data =  $this->get_relations();
		if(count($relations_data) > 0){
		foreach($relations_data as $rel=>$relations){
			// echo '<pre>';
			// print_r($relations);
			// echo '<pre>';
			
			 
        $default_addressarr = $relations->default_address;
        $website = $relations->website;
        $pricelist_id = $relations->pricelist_id; 
        $paymentcondition_id = $relations->paymentcondition_id; 
        $relationtype_code = $relations->relationtype_code; 
        $customer_pricelist_id = $relations->customer_pricelist_id; 
        $promotional_pricelist_id = $relations->promotional_pricelist_id; 
        $default_email = $relations->default_email; 
        $relation_addresses = $relations->relation_addresses; 
        $relation_addressesarr = $relations->relation_addresses; 
        $vat_nr = $relations->vat_nr;
        $default_phone = $relations->default_phone;
        $default_invoice_addressarr = $relations->default_invoice_address;
        $language_code = $relations->language_code;
        $default_delivery_addressarr = $relations->default_delivery_address;
        $deliverycondition_id = $relations->deliverycondition_id;
        $legal_form = $relations->legal_form;
        $default_fax = $relations->default_fax;
        $customer_id = $relations->customer_id;
        $sku = $relations->customer_id;

        $firstname = 'Admin';
        $lastname = 'New';
        $email = 'customrr@custom.com';
        $websiteid = '1';

        $customer = $objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId(1)->loadByEmail('$email');
          //$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($sku);
         // $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->loadByAttribute('sku',$sku);
          if($customer){
          // 	echo 'update';
          // 	$this->update_product($firstname,$lastname,$email);
          // }else{
          	echo 'Insert';
          	$this->insert_customer($firstname,$lastname,$email,$websiteid);
          }
           

       
		}
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

	private function get_relations(){
		echo $url = $this->api_url.$this->client_id.'/relations/customers';
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		if($result == False){
			echo " Relations not found  ";
			echo curl_error($ch);
		} else {
	        return json_decode($result);
		}
	}



  public function insert_customer($firstname,$lastname,$email,$websiteid){
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$customer = $objectManager->create('\Magento\Customer\Model\CustomerFactory');

    $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory'); 
    $customer=$customerFactory->create();
    $customer->setWebsiteId($websiteid);
    $customer->loadByEmail('$email');// load customer by email address
    //echo $customer->getEntityId();
    $customer->load('1');// load customer by email address
    $data= $customer->getData();
        $customer->setLastname("$lastname");
        $customer->setFirstname("$firstname");
        $customer->setemail("$email");

        $customer->save();



}
	public function refreshCache(){
		$command = 'php bin/magento cache:clean && php bin/magento cache:flush';
		shell_exec($command);	
		echo '<li><i class="fa fa-check" aria-hidden="true"></i>';
		echo 'Cache Cleaned Successfully';
		echo '</li>';
	}  
    }
    
