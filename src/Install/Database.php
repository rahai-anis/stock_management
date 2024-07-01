<?php
declare(strict_type=1);
namespace Anis\Stockmanagement\Install;

class Database{
    public static function installQueries(): array{
        $queries = [];
    
        $queries[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "stock_hattaa` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
             `id_product` VARCHAR(255) NOT NULL,
             `id_attribute` VARCHAR(255) NOT NULL,
             `name` VARCHAR(255) NOT NULL,
             `supplier` VARCHAR(255) NOT NULL,
             `attribut_type` VARCHAR(255) NOT NULL,
             `attribute_value` VARCHAR(255) NOT NULL,
             `quantity` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE='" ._MYSQL_ENGINE_. "' DEFAULT CHARSET=utf8;";
        return $queries;
    }
    public static function uninstallQueries(): array
    {
        $queries = [];
    
        $queries[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'stock_hattaa`';
       
       
    
        return $queries;
    }
}