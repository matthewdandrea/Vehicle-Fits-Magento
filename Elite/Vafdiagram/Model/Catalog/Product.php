<?php 
class Elite_Vafdiagram_Model_Catalog_Product
{
	/** @var Elite_Vaf_Model_Catalog_Product */
    protected $wrappedProduct;
   
    function __construct(Elite_Vaf_Model_Catalog_Product $productToWrap )
    {
        $this->wrappedProduct = $productToWrap;
    }

    function addServiceCode($serviceCode, $paramaters = array() )
    {
    	$select = $this->getReadAdapter()->select()
            ->from('elite_product_servicecode')
            ->where('product_id = ?', (int)$this->getId())
            ->where('service_code = ?', $serviceCode);
        for($level=1; $level<=4; $level++)
        {
	    	if(isset($paramaters['category1']) && !is_null($paramaters['category1']))
	        {
	        	$select->where('category1_id = ?', $paramaters['category1']);
	        }
        }
        
        $result = $select->query();
        if($result->fetchColumn())
        {
            return;
        }
        
        $values = array(
            'product_id' => (int)$this->getId(),
            'service_code' => $serviceCode
       	);
       	
       	if(isset($paramaters['illustration_id']))
       	{
       		$values = $values + array(
        		'illustration_id' => $paramaters['illustration_id']
       		);
       	}
       	
       	for($level=1; $level<=4; $level++)
        {
	       	if(isset($paramaters['category'.$level]) && $paramaters['category'.$level])
	        {
		       	$values = $values + array(
		        	'category'.$level.'_id' => $paramaters['category'.$level]
		        );
	        }
        }
        $this->getReadAdapter()->insert('elite_product_servicecode', $values);
    }
    
    function serviceCodes($category1=null,$category2=null,$category3=null,$category4=null)
    {
    	$select = $this->getReadAdapter()->select()
            ->from('elite_product_servicecode', array('product_id','service_code'))
            ->where('product_id = ?', (int)$this->getId() );
        if(!is_null($category1))
        {
        	$select->where('category1_id = ?', $category1);
        }
        if(!is_null($category2))
        {
        	$select->where('category2_id = ?', $category2);
        }
        
        if(!is_null($category3))
        {
        	$select->where('category3_id = ?', $category3);
        }
        
        if(!is_null($category4))
        {
        	$select->where('category4_id = ?', $category4);
        }
        
        $result = $this->query($select);
        
        $return = array();
        foreach( $result->fetchAll(Zend_Db::FETCH_OBJ) as $row )
        {
            array_push($return, $row->service_code );
        }
        return $return;
    }
    
    function illustrationId($paramaters)
    {
    	$select = $this->getReadAdapter()->select()
            ->from('elite_product_servicecode', array('illustration_id'))
            ->where('product_id = ?', (int)$this->getId() )
            ->where('service_code = ?', $paramaters['service_code'] );
        if(!is_null($paramaters['category1']))
        {
        	$select->where('category1_id = ?', $paramaters['category1']);
        }
        $result = $this->query($select);
        return $result->fetchColumn();
    }
    
    function __call($methodName,$arguments)
    {
        $method = array($this->wrappedProduct,$methodName);
        return call_user_func_array( $method, $arguments );
    }
    
	function query($sql)
    {
    	return $this->getReadAdapter()->query($sql);
    }
    
 	/** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return Elite_Vaf_Helper_Data::getInstance()->getReadAdapter();
    }
}