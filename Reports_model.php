<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends MY_Model {
   public function __construct()
   {
       parent::__construct();
   }
   

   
   

public function userdetails_religious_infoByid($user_id)
   {
         $this->db->where('user_id',$user_id);
         $query = $this->db->get('user_details_religious');
         $result = array();
         if($query->num_rows() > 0)
         {
             $result = $query->row();
         }
         return $result;
   }
   
   public function getReport($boyId,$girlId)
   {
         $this->db->where('male_id',$boyId);
         $this->db->where('female_id',$girlId);
         $query = $this->db->get('horoscope_info');
         $result = array();
         if($query->num_rows() > 0)
         {
             $result = $query->row();
         }
         return $result;
   }
   
   
    public function getOrdersCount($storeId,$aptId,$status)
   {
        $sqlquery= 'sum(o.total) as total,count(o.id) as count';

        
        if(!empty($storeId)){
            $this->db->where('o.store_id',$storeId);
        }
        
         if(!empty($status)){
            if(strcmp($status, 'inprogress') == 0){
                
            $where_in = array('1','2','3','6');
             $this->db->where_in('o.order_status',$where_in);
            }else if(strcmp($status, 'pending') == 0){
                   $this->db->where_in('o.order_status',7); 
            }else if(strcmp($status, 'completed') == 0){
                    $this->db->where_in('o.order_status',4);
            }else if(strcmp($status, 'cancelled') == 0){
                    $this->db->where_in('o.order_status',5);
            }      
         }
       
                
        
        if(!empty($aptId)){
            $this->db->where('o.apt_id',$aptId);
        }
    
        $this->db->select($sqlquery);
         $query = $this->db->get('orders o');
         $result = array();
         if($query->num_rows() > 0)
         {
             $result = $query->row();
         }
         return $result;
         
   }
   
  public function getWalletSum($payment_status,$status)
  {
        $sqlquery= 'sum(o.total) as sum';
    
        
        if(!empty($status)){
                if(strcmp($status, 'inprogress') == 0){
                
               
            $where_in = array('1','2','3','6');
             $this->db->where_in('o.order_status',$where_in);
            }else if(strcmp($status, 'pending') == 0){
                  $this->db->where_in('o.order_status',7); 
            }else if(strcmp($status, 'completed') == 0){
                    $this->db->where('o.order_status',4);
            }else if(strcmp($status, 'cancelled') == 0){
                    $this->db->where_in('o.order_status',5);
            }
        }
        
           if(!empty($payment_status)){
            
                    if(strcmp($payment_status, 'cod') == 0){
                    
                $where_in = array('PAID_BY_COD','PAID_BY_MACHINE');
                 $this->db->where_in('o.payment_status',$where_in);
                }else if(strcmp($payment_status, 'online') == 0){
                      $this->db->where('o.payment_status','PAID_BY_PAYTM'); 
                }else if(strcmp($payment_status, 'machine') == 0){
                        $this->db->where('o.payment_status','PAID_BY_MACHINE');
                }else if(strcmp($payment_status, 'wallet') == 0){
                        $this->db->where('o.payment_status','PAID_BY_WALLET');
                }   
           }
   
    
        $this->db->select($sqlquery);
         $query = $this->db->get('orders o');
         $result = array();
         if($query->num_rows() > 0)
         {
             $result = $query->row();
         }
         return $result;
  }
  public function getUserCount($role,$status)
   {
        $sqlquery= 'count(u.id) as total';
        
         if(!empty($status)){
    
         $this->db->where('u.status',$status);
        
        }
        
        if(!empty($role)){
    
         $this->db->where('u.role',$role);
        
        }
        
        
    
        $this->db->select($sqlquery);
         $query = $this->db->get('user u');
         
         if($query->num_rows() > 0)
         {
             $result = $query->row();
         }
         return $result->total;
   }
    public function getMonthlyReport($store_id,$startDate,$endDate)
   {
        $sqlquery= "select year(dated) as year,MONTHNAME(dated) as month,sum(total) as totalAmount,COUNT(id) as totalorders,AVG(total) from orders where dated between '$startDate' AND '$endDate' AND store_id=$store_id group by year,month order by year,month(dated) DESC";
        
        
    
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
    public function getTopTenItems($store_id)
  {
        $sqlquery= "select order_items.menu_item_id,menu_item.name,menu_item_price.product_id, COUNT(*) AS count,SUM(menu_item_price.price) AS amount FROM order_items,menu_item,menu_item_price WHERE order_items.menu_item_id=menu_item.id AND menu_item_price.product_id=menu_item.id AND order_items.store_id=$store_id GROUP BY menu_item_id ORDER BY COUNT(*) DESC LIMIT 10";
        
        
    
      $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
  }
  
  public function getBottomTenItems($store_id)
  {
        $sqlquery= "select order_items.menu_item_id,menu_item.name,menu_item_price.product_id, COUNT(*) AS count,SUM(menu_item_price.price) AS amount FROM order_items,menu_item,menu_item_price WHERE order_items.menu_item_id=menu_item.id AND menu_item_price.product_id=menu_item.id AND order_items.store_id=$store_id GROUP BY menu_item_id ORDER BY COUNT(*), amount ASC LIMIT 0,10";
        
        
    
      $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
  }
  public function getUserCategorySpent($mobile_num)
  {
        $sqlquery="SELECT category.name AS categoryname,orders.username,SUM(total) FROM category,menu_item,order_items,orders WHERE category.id=menu_item.category_id AND order_items.menu_item_id=menu_item.id AND orders.id=order_items.order_id and mobile IN($mobile_num) GROUP BY username,category_id ORDER BY category_id";

        
    
      $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
  }
   public function getUserMonthlyCategory($startDate,$endDate,$mobile)
   {
       
       
       
        $sqlquery= "SELECT category.name AS categoryname,orders.username,SUM(total),year(dated) as year,MONTHNAME(dated) as month FROM category,menu_item,order_items,orders WHERE category.id=menu_item.category_id AND order_items.menu_item_id=menu_item.id AND orders.id=order_items.order_id  AND mobile IN ($mobile) AND dated BETWEEN '$startDate' AND '$endDate'GROUP BY category_id, month ORDER BY category_id AND year,month(dated) DESC";

//echo $sqlquery;
    
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   public function getTodaySalesCount($store_id)
   {
       
       
       
        $sqlquery= "select COUNT(id) FROM orders WHERE date(dated)= CURDATE()";

//echo $sqlquery;
    
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   public function getPeriodicSaleReport($store_id,$range){
    
    $sql ="";
    if(strcmp($range, 'today') == 0){
    $sql ="select sum(total) as total,sum(final_amount) as final_amount,COUNT(id) as OrdersCount,store_id FROM orders WHERE date(dated)= CURDATE() AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'yesterday') == 0){
    $sql ="select sum(total) as total,sum(final_amount) as final_amount,COUNT(id) as OrdersCount,store_id FROM orders WHERE date(dated)= CURDATE()-1 AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'lastweek') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id FROM orders WHERE YEARWEEK(dated)=YEARWEEK(NOW())-1 AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'lastmonth') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE MONTH(dated)=MONTH(curdate())-1 AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'total') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE store_id IN ($store_id)";    
    }else if(strcmp($range, 'CODtotal') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE payment_mode='COD' AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'PAYTMtotal') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE payment_mode='PAY_TM' AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'CARDtotal') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE payment_mode='CARD' AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'SWIPEtotal') == 0){
    $sql ="SELECT sum(total) as total,sum(final_amount) as final_amount,COUNT(*) as OrdersCount,store_id from orders WHERE payment_mode='SWIPE' AND store_id IN ($store_id)";    
    }else if(strcmp($range, 'INPROGRESStotal') == 0){
    $sql ="SELECT SUM(total) AS total,sum(final_amount) as final_amount,COUNT(id) AS OrdersCount,store_id FROM `orders` WHERE order_status=7 AND store_id IN ($store_id)";
    }else if(strcmp($range, 'COMPLETEDtotal') == 0){
    $sql ="SELECT SUM(total) AS total,sum(final_amount) as final_amount,COUNT(id) AS OrdersCount,store_id FROM `orders` WHERE order_status=4 AND store_id IN ($store_id)";
    }else if(strcmp($range, 'CANCELLEDtotal') == 0){
    $sql ="SELECT SUM(total) AS total,sum(final_amount) as final_amount,COUNT(id) AS OrdersCount,store_id FROM `orders` WHERE order_status=5 AND store_id IN ($store_id)";
    }else if(strcmp($range, 'ORDERStotal') == 0){
    $sql ="SELECT SUM(total) AS total,sum(final_amount) as final_amount,COUNT(id) AS OrdersCount,store_id FROM `orders` WHERE store_id IN ($store_id)";
    }

    
    $query = $this->db->query($sql);
    
    $result = array();
    if($query->num_rows() > 0)
         {
             //$result = $query->result_array();
              $result = $query->row();
            
    }
         return $result;
    
}

public function getHourlySales($date,$store_id)
   {
       
        $sqlquery= "select hour(dated) AS hour, COUNT(id) AS NoOfOrders
     , SUM(total) as Total 
from orders
WHERE dated
      BETWEEN '$date 00-00-00' AND '$date 23-59-59' AND store_id=$store_id 
group by hour
order by  (CURDATE() + INTERVAL (SELECT hour(NOW())) hour - INTERVAL 23 hour)";

//echo $sqlquery;
    
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }

public function getMonthlyOrders($store_id,$startDate,$endDate)
   {
        $sqlquery= "select username,mobile,dated,total,tax,ROUND(final_amount) from orders where dated between '$startDate' AND '$endDate' AND store_id=$store_id";
        
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
    public function getProductwiseSale($startDate,$endDate,$store_id)
   {
        $sqlquery= "SELECT name as productname ,SUM(price) as totalamount, sizeValue as size, SUM(quantity) as quantity, orders.dated as Date  FROM `order_items`,orders
WHERE orders.id=order_items.order_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_items.store_id=$store_id
GROUP BY sizeValue,name
ORDER BY quantity DESC";
        
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
   public function getBillReport($store_id,$startDate,$endDate)
   {
        $sqlquery= "SELECT bill_no AS Order_No,dated as OrderDate,payment_mode,total AS My_Amount,tax/2 AS CGST,tax/2 AS SGST,ROUND(final_amount) AS Total_Amount FROM `orders` WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59 ORDER BY dated'";
        
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }

   
   public function getCategoryReport($startDate,$endDate,$store_id)
   {
        $sqlquery= "SELECT SUM(price) as totalamount,category.name,SUM(quantity) as quantity,orders.dated as Date
FROM `order_items`,category,menu_item,orders
WHERE category.id=menu_item.category_id AND order_items.menu_item_id=menu_item.id AND orders.id=order_items.order_id
AND orders.dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_items.store_id=$store_id
GROUP BY category.name
ORDER BY quantity DESC";
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
    public function getCategoryProductwiseSale($startDate,$endDate,$store_id)
   {
        $sqlquery= "SELECT category.name AS CategoryName,order_items.name as productname ,SUM(order_items.price) as totalamount, sizeValue as size, SUM(quantity) as quantity, orders.dated as Date  
FROM order_items,orders,category,menu_item
WHERE category.id=menu_item.category_id AND order_items.menu_item_id=menu_item.id AND orders.id=order_items.order_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_items.store_id=$store_id
GROUP BY sizeValue,order_items.name
ORDER BY category.name,quantity DESC";
        
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
   public function getExecutiveSalesReport($store_id,$startDate,$endDate,$range){
    
    $sql ="";
    if(strcmp($range, 'BillingSuccess') == 0){
    $sql ="SELECT COUNT(*) AS OrderCount,SUM(total) AS NetSale,SUM(shipping_address) as DeliveryCharge,SUM(tax/2) AS CGST,SUM(tax/2) AS SGST,SUM(final_amount) AS GrandTotal,store_id FROM `orders` WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_status=4";    
    }else if(strcmp($range, 'BillingCancel') == 0){
    $sql ="SELECT COUNT(*) AS OrderCount,SUM(total) AS NetSale,SUM(shipping_address) as DeliveryCharge,SUM(tax/2) AS CGST,SUM(tax/2) AS SGST,SUM(final_amount) AS GrandTotal,store_id FROM `orders` WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_status=5";    
    }else if(strcmp($range, 'orderType') == 0){
    $sql ="SELECT COUNT(*) AS OrderCount,platform,SUM(total) AS Total,store_id FROM orders WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_status=4 GROUP BY platform";    
    }else if(strcmp($range, 'paymentMode') == 0){
    $sql ="SELECT COUNT(*) AS OrderCount,payment_mode,SUM(total) AS Total,store_id FROM `orders` WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_status=4 GROUP BY payment_mode";    
    }
    

    $dataResult =  $this->db->query($sql);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         
         return $result[0];
   }
   
   public function getPaymentModeReport($store_id,$startDate,$endDate)
   {
        $sqlquery= "SELECT COUNT(*) AS OrderCount,payment_mode,SUM(total) AS Total,store_id FROM `orders` WHERE store_id=$store_id AND dated BETWEEN '$startDate 00-00-00' AND '$endDate 23-59-59' AND order_status=4 GROUP BY payment_mode";
        
       $dataResult =  $this->db->query($sqlquery);
       
         $result = array();
         if($dataResult->num_rows() > 0)
         {
             $result = $dataResult->result_array();
         }
         return $result;
   }
   
   

}