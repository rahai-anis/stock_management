<?php
declare(strict_type=1);
require_once __DIR__ . '/vendor/autoload.php';
use Anis\Stockmanagement\Install\Installer;

use Anis\Stockmanagement\Controller\LogsController;
class Stockmanagement extends Module
{
public function __construct()
    {
        $this->name = 'stockmanagement';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Anis';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('stockmanagement', [], 'Modules.Stockmanagement.Admin');
        $this->description = $this->trans("stockmanagement", [], 'Modules.Stockmanagement.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        
        
       
       
    }
    public function install()
    {


        if (!parent::install()) {
            return false;
        }
        $installer = new Installer;

        return $this->registerHook('actionOrderStatusUpdate') && $this->registerHook('actionValidateOrderAfter') && $installer->install($this);
    }
    public function uninstall()
    {


        if (!parent::uninstall()) {
            return false;
        }
        $installer = new Installer;
        return $installer->unInstall() && parent::uninstall();

    }
    public function getContent()
    {
        $logFile = _PS_MODULE_DIR_ . $this->name . '/logs/log.txt';
       dump($logFile);die;
    }
    public function hookmoduleRoutes()
    {

    }
    public function hookActionValidateOrderAfter($params)
    {
      
        $product = $params['orders'][0]->product_list;
        for($i=0;$i<count($product);$i++){
            $sql = "UPDATE " . _DB_PREFIX_ . "stock_hattaa 
            SET quantity = CASE
                WHEN quantity >= " . $product[$i]['quantity'] . " THEN quantity - " . $product[$i]['quantity'] . "
                ELSE 0
            END
            WHERE id_attribute = " . $product[$i]['id_product_attribute'] . ";";
    
            if(\Db::getInstance()->execute($sql)){
             
                $message = date('Y-m-d H:i:s') ." : Product with ID attribute {$product[$i]['id_product_attribute']} has decreased by {$product[$i]['quantity']} pieces \n ";
              
            }else{
                $message = date('Y-m-d H:i:s') ." : Failed to decrease quantity by {$product[$i]['quantity']} to Product with ID attribute {$product[$i]['id_product_attribute']}  \n ";
              
            }
            $logsController = new LogsController;
            $logsController->addLog($message);
        }
        
    }
    public function hookActionOrderStatusUpdate($params)
    {
        $retour_etat = [9, 10, 11, 12];
        $orderStatus = $params['newOrderStatus']->id;
    
        if (in_array($orderStatus, $retour_etat)) {
            $idOrder = (int)$params['id_order'];
            $productAttrIds = $this->getProductAttributesByOrderId($idOrder); // Assuming this function retrieves the product attributes for the given order ID
    
            foreach ($productAttrIds as $productAttr) {
                $sql = "UPDATE `" . _DB_PREFIX_ . "stock_hattaa` 
                        SET `quantity` = `quantity` + " . (int)$productAttr['product_quantity'] . " 
                        WHERE `id_attribute` = " . (int)$productAttr['product_attribute_id'] . ";";
    
                        if(\Db::getInstance()->execute($sql)){
             
                            $message = date('Y-m-d H:i:s') ." : Product with ID attribute {$product[$i]['id_product_attribute']} has increased by {$product[$i]['quantity']} pieces \n ";
                          
                        }else{
                            $message = date('Y-m-d H:i:s') ." : Failed to increase quantity by {$product[$i]['quantity']} to Product with ID attribute {$product[$i]['id_product_attribute']}  \n ";
                          
                        }
                        $logsController = new LogsController;
                        $logsController->addLog($message);
            }
        }
    }
    
    /**
     * Retrieve product attributes by order ID.
     * 
     * @param int $idOrder The order ID.
     * @return array The product attributes.
     */
    private function getProductAttributesByOrderId($idOrder)
    {
        $sql = "SELECT `product_attribute_id`, `product_quantity` 
                FROM `" . _DB_PREFIX_ . "order_detail` 
                WHERE `id_order` = " . (int)$idOrder . ";";
    
        return \Db::getInstance()->executeS($sql);
    }
    
}