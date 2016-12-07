<?php
/**
 * Copyright © 2016 EvatoErp. All rights reserved.
 */

namespace Evato\Globis\Controller\Adminhtml\Categories;

class SyncCategories extends \Evato\Globis\Controller\Adminhtml\Categories
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
		//$categories =  $this->get_categories();
		// echo "<pre>";
		// 	print_r($categories);
		// echo "</pre>";


       
		
		
			$websiteId = $storeManager->getWebsite()->getWebsiteId();
			echo 'websiteId: '.$websiteId." ";

			/// Get Store ID
			$store = $storeManager->getStore();
			$storeId = $store->getStoreId();
			echo 'storeId: '.$storeId." ";

			/// Get Root Category ID
			$rootNodeId = $store->getRootCategoryId();
			echo 'rootNodeId: '.$rootNodeId." "; 
			/// Get Root Category
			$rootCat = $objectManager->get('Magento\Catalog\Model\Category');
			$cat_info = $rootCat->load($rootNodeId);

		    $group_data =  $this->get_groups();
				if(count($group_data) > 0){
				foreach($group_data as $x=>$groups){
		            
		             // echo "<pre>";
					 // print_r($groups);
				     // echo "</pre>";
				     // die;
				     $main_cat_id = $groups->articlemaingroup_id; 
					 $main_cat_name = $groups->articlemaingroup_code;
					 $main_cat_desc = $groups->description[0]->value;
					 $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
					 $cat = $categoryCollection->create()->addAttributeToSelect('*')->addAttributeToFilter('glob_group_id', $main_cat_id);
			         // echo '<pre>';
			         // print_r($cat->getdata());
			         // echo '</pre>';
			        
			         
              
			        if($cat->getdata()){
				        echo 'update';
				        $parent_categoryId = $cat->getFirstItem()->getId();
				        $parent_category_path = $cat->getFirstItem()->getPath(); 
			          	$this->update_maincategory($main_cat_id,$main_cat_name,$main_cat_desc,$rootCat,$storeId,$rootNodeId,$parent_categoryId);
		            }else{
				        echo 'Insert';
				        $this->insert_maincategory($main_cat_id,$main_cat_name,$main_cat_desc,$rootCat,$storeId,$rootNodeId);
				        $cat_saved = $categoryCollection->create()->addAttributeToSelect('*')->addAttributeToFilter('glob_group_id', $main_cat_id);
				        if($cat_saved->getdata()){
				        	$parent_categoryId = $cat_saved->getFirstItem()->getId();
				        	$parent_category_path = $cat_saved->getFirstItem()->getPath(); 
				        }
			        }

				    //$articlesubgroup_id = $groups->articlesubgroup_id; 
					$main_categoryarr = $groups->subgroups;
					foreach ($main_categoryarr as $key => $value) {
							 // echo "<pre>";
							 // print_r($main_categoryarr);
						  //    echo "</pre>";
				      
				      $articlesubgroup_code = $value->articlesubgroup_code;
				      $glob_subgroup_id = $value->articlesubgroup_id; 
				      //$des_arr = $value->description; 
				      $sub_cat_desc = $value->description[0]->value; 
				     // $des_arr = $value->description;
                   

                    $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
					$cat = $categoryCollection->create()->addAttributeToSelect('*')->addAttributeToFilter('glob_group_id', $glob_subgroup_id);
			         echo '<pre>';
			         print_r($cat->getdata());
			         echo '</pre>';

				
			        if($cat->getdata()){
			        echo 'update';
			       echo $parent_categoryId = $cat->getFirstItem()->getId();
			        //$parentcategoryId = $cat->getFirstItem()->getId(); 
			         //$categoryId = $cat->getFirstItem()->getId();

		          	$this->update_subcategory($main_cat_id,$glob_subgroup_id,$articlesubgroup_code,$parent_categoryId,$storeId,$parent_category_path,$sub_cat_desc);
		            }else{
			        echo 'Insert';

			        $this->insert_subcategory($main_cat_id,$glob_subgroup_id,$articlesubgroup_code,$parent_categoryId,$storeId,$parent_category_path,$sub_cat_desc);
			        }


				     
                   } 

		die;			
					//print_r($des_arr);
				  //$articlemaingroup_code = $groups->articlemaingroup_code; 

             
					
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
	
	private function get_groups(){
		$url = $this->api_url.$this->client_id.'/article/groups';
		$headers[] = 'Authorization:'.$this->access_token;
		$headers[] = 'Content-Type: Application/json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		if($result == False){
			echo " groups not found  ";
			echo curl_error($ch);
		} else {
	        return json_decode($result);
		}
	}
	
         
         public function insert_MainCategory($main_cat_id,$main_cat_name,$main_cat_desc,$rootCat,$storeId,$rootNodeId){

         	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
               $categoryFactory=$objectManager->get('\Magento\Catalog\Model\CategoryFactory');
				 $name=ucfirst($main_cat_id);
				 $url=strtolower($main_cat_id);
				//$cleanurl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($url))))));
				$categoryFactory=$objectManager->get('\Magento\Catalog\Model\CategoryFactory');
				/// Add a new sub category under root category
				$category = $categoryFactory->create();
				$category->setName($main_cat_name);
				$category->setIsActive(true);
				//$category->setUrlKey($cleanurl);
				$category->setData('glob_group_id', $main_cat_id);
				$category->setData('description', $main_cat_desc);
				$category->setParentId($rootCat->getId());
				//$mediaAttribute = array ('image', 'small_image', 'thumbnail');
				//$category->setImage('/m2.png', $mediaAttribute, true, false);// Path pub/meida/catalog/category/m2.png
				$category->setStoreId($storeId);
				$category->setPath($rootCat->getPath());
				$category->save();
				}

public function update_MainCategory($main_cat_id,$main_cat_name,$main_cat_desc,$rootCat,$storeId,$rootNodeId,$parent_categoryId){

 	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
 	    $category = $objectManager->get('\Magento\Catalog\Model\Category')->load($parent_categoryId);
 	   
 	    if($category){


 	    	$cat_data = array();
 	    	$cat_data['entity_id'] = $parent_categoryId;
 	    	$cat_data['name'] = $main_cat_name;
 	    	$cat_data['description'] = $main_cat_desc;
 	    	$cat_data['glob_group_id'] = $main_cat_id;
			//$cat_data["store_id"] = $storeId;
			// $cat_data["store"] = $storeId;
			// $cat_data["storeid"] = $storeId;
 	    	$cat_data['is_active'] = 1;
     
			$category->setData($cat_data);
			$category->setStoreId($storeId);
		
 			$categoryFactory=$objectManager->get('\Magento\Catalog\Api\CategoryRepositoryInterface')->save($category);

		}
	}

public function insert_subcategory($main_cat_id,$glob_subgroup_id,$articlesubgroup_code,$parent_categoryId,$storeId,$parent_category_path,$sub_cat_desc){

         	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
              
				$categoryFactory=$objectManager->get('\Magento\Catalog\Model\CategoryFactory');
				/// Add a new sub category under root category
				$category = $categoryFactory->create();
				$category->setName($articlesubgroup_code);
				$category->setIsActive(true);
				$category->setData('glob_maingroup_id', $main_cat_id);
				$category->setData('glob_group_id', $glob_subgroup_id);
				$category->setData('description', $sub_cat_desc);
				$category->setParentId($parent_categoryId);
				//$mediaAttribute = array ('image', 'small_image', 'thumbnail');
				//$category->setImage('/m2.png', $mediaAttribute, true, false);// Path pub/meida/catalog/category/m2.png
				$category->setStoreId($storeId);
				$category->setPath($parent_category_path);
				$category->save();
				}

         public function update_subcategory($main_cat_id,$glob_subgroup_id,$articlesubgroup_code,$parent_categoryId,$storeId,$parent_category_path,$sub_cat_desc){

 	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
 	    $sub_category = $objectManager->get('\Magento\Catalog\Model\Category')->load($parent_categoryId);
 	   
 	    if($sub_category){

 	    	 $subcat_data = array();
 	    	
 	    	echo $subcat_data['entity_id'] = $parent_categoryId; 
 	    	echo $subcat_data['subcat_name'] = $articlesubgroup_code;
 	    	echo $subcat_data['description'] = $sub_cat_desc;
 	    	echo $subcat_data['glob_maingroup_id'] = $main_cat_id;
 	    	echo $subcat_data['glob_group_id'] = $glob_subgroup_id;
 	    	$subcat_data['is_active'] = 1;
     
			$sub_category->setData($subcat_data);
			$sub_category->setStoreId($storeId);
		
 			$categoryFactory=$objectManager->get('\Magento\Catalog\Api\CategoryRepositoryInterface')->save($sub_category);

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

?>