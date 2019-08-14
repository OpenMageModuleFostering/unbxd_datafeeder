<?php


class Unbxd_Datafeeder_Model_Observer
{		

	
	public function feed()
	{
		 $allowedvisibility = array(
			  array(
			    "finset" => array(3)
			  ),
			  array(
			    "finset" => array(4)
			  ),
			);	
		 $collection = Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('visibility',$allowedvisibility);
		 $totalsize = $collection->getSize();

		 $pageStart=1;
		 $pageSize=50;	
		 set_time_limit(0);	

		for($pageStart=1;$pageStart<=$totalsize;$pageStart=$pageStart+$pageSize)
		{
			if($totalsize-$pageStart<$pageSize)
				$pageSize=$totalsize-$pageStart;
			$collection = Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('visibility',$allowedvisibility);
			$collection->clear()
			   ->setPage($pageStart, $pageSize)
		           ->load();
			$resultarray=array();
			$resultarray["data"]=array();
		
			foreach($collection as $item)
			{
				$result=array();
				foreach($item->getData('') as $columnHeader=>$columndata)
				{
					if(!is_object($columndata))
					{
						if($item->getAttributeText($columnHeader))
							$columndata=$item->getAttributeText($columnHeader);

						$result[$columnHeader]=urlencode(addslashes($columndata));
	
					}
					$categoryIds = $item->getCategoryIds();

					$categoryarray=array();
					$categoryurlarray=array();
					foreach($categoryIds as $categoryId) {
					    $category = Mage::getModel('catalog/category')->load($categoryId);
					    $categoryarray[]=urlencode(addslashes($category->getName()));
					    $categoryurlarray[]=urlencode(addslashes($category->getUrlPath()));
					 }
					$result["category"]=$categoryarray;
				$result["categoryurl"]=$categoryurlarray;
	 
				}
				$resultarray["data"][]=$result;
			}


			$url = 'http://176.9.111.205:8080/skoolshop_an/datafeed';

			$vars = 'jsondoc=' .json_encode($resultarray) ;

			 $ch = curl_init($url);
			 curl_setopt($ch, CURLOPT_POST ,1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
			 curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			 curl_setopt($ch, CURLOPT_HEADER ,0);  
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1); 
			 $response = curl_exec($ch);

		}
   

	}

}
