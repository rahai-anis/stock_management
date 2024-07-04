<?php
namespace Anis\Stockmanagement\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Stockmanagement extends FrameworkBundleAdminController
{
    
   
    public function home(Request $request)
    {
        $page = $request->query->getInt('page', 1); // Get the current page number from the query parameters, default to 1 if not provided
        $limit = 20; // Number of results per page
    
        // Calculate the offset based on the current page and limit
        $offset = ($page - 1) * $limit;
        $sql ="SELECT pa.id_product, pa.id_product_attribute, pa.reference, sa.quantity as quantity_stock_available, sh.quantity as quantity_stock_hattaa

            FROM `"._DB_PREFIX_."product_attribute` pa
            LEFT JOIN `"._DB_PREFIX_."stock_available` sa ON pa.id_product_attribute = sa.id_product_attribute
            LEFT JOIN 
                        `"._DB_PREFIX_."stock_hattaa` sh ON pa.id_product_attribute = sh.id_attribute
            WHERE pa.id_product_attribute != 0
            ORDER BY 
                    pa.id_product DESC
            LIMIT $limit OFFSET $offset;
            

";
      
        $results = \Db::getInstance()->executeS($sql);
        $countSql = "SELECT COUNT(*) AS total 
        FROM `"._DB_PREFIX_."product_attribute` pa
        LEFT JOIN `"._DB_PREFIX_."stock_available` sa ON pa.id_product_attribute = sa.id_product_attribute
        WHERE pa.id_product_attribute != 0;";
$totalCount = \Db::getInstance()->getValue($countSql);
       
        return $this->render(
            '@Modules/stockmanagement/views/templates/twig/home.html.twig',
            [
                'enableSidebar' => true,
                'results' => $results,
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'perPage' => $limit,
            ]
        );
    }
    public function update_stock_hattaa()
    {
         return $this->render(
            '@Modules/stockmanagement/views/templates/twig/update_stock_hattaa.html.twig',
            [
                'enableSidebar' => true,
               
            ]
        );
    }
    public function handle_upload()
    {
         
        $rows = explode("\n", trim($_POST['content']));
        //dump($rows);
        $titles = explode(',', trim($rows[0]));
       
        // Define expected column names
        $expectedTitles = array('id_product', 'id_attribute', 'name', 'supplier', 'attribut_type', 'attribute_value', 'quantity');
        
        // Check if the extracted titles match the expected titles
        if (count($titles) != count($expectedTitles)) {
           $error = "Error: Number of columns doesn't match expected.";
            
            
           
            return $this->redirectToRoute(
                'update_stock_hattaa',
                [
                    'exeption' => $error,
                    'type' => 'danger',
                ]
            );
        }
        
        foreach ($expectedTitles as $index => $expectedTitle) {
            if ($titles[$index] != $expectedTitle) {
               $error = "Error: Column '{$titles[$index]}' at index $index doesn't match expected '{$expectedTitle}'.";
               
               return $this->redirectToRoute(
                'update_stock_hattaa',
                [
                    'exeption' => $error,
                    'type' => 'danger',
                ]
            );
            }
        }
        
        $sqlTemplate = "INSERT INTO `" . _DB_PREFIX_ . "stock_hattaa`
                    (`id_product`, `id_attribute`,  `name`, `supplier`, `attribut_type`, `attribute_value`, `quantity`)
                    VALUES ";
      array_shift($rows);
    // Initialize variables
    $chunkSize = 1000; // Adjust as needed
    $rowCount = count($rows) - 1; // Exclude header row if present
    $successCount = 0;
     $delete_query = "TRUNCATE TABLE " . _DB_PREFIX_ . "stock_hattaa;";   
     \Db::getInstance()->execute($delete_query);
    // Loop through rows and process in chunks
    for ($start = 1; $start <= $rowCount; $start += $chunkSize) {
        // Build the SQL query for this chunk
        $sqlInsert = $sqlTemplate;
        $params = [];

        // Process each chunk
        for ($i = $start; $i < $start + $chunkSize && $i <= $rowCount; $i++) {
            // Split row into columns
            $columns = explode(',', trim($rows[$i]));

            // Prepare values for insertion
            $id_product = (int)$columns[0];
            $id_attribute = (int)$columns[1];
            //$reference = pSQL($columns[2]);
            $name = pSQL($columns[2]);
            $supplier = pSQL($columns[3]);
            $attribute_type = pSQL($columns[4]);
            $attribute_value = pSQL($columns[5]);
            $quantity = pSQL($columns[6]);

            // Add values to batch insert array
            $params[] = "($id_product, $id_attribute, '$name', '$supplier', '$attribute_type', '$attribute_value', '$quantity')";
        }

        // Join all value strings
        $sqlInsert .= implode(', ', $params);

        // Execute the insert query
        if (\Db::getInstance()->execute($sqlInsert)) {
            $successCount += count($params); // Increment success count
        } else {
          //  echo "Failed to insert chunk starting at row $start.<br>";
          $error = "Failed to insert chunk starting at row $start";
           
            
            return $this->redirectToRoute(
                'update_stock_hattaa',
                [
                    'exeption' => $error,
                    'type' => 'danger',
                ]
            );
        }
    }

    //echo "Successfully inserted $successCount rows.<br>";
        $message ="Successfully inserted $successCount rows";
    
    
            
            return $this->redirectToRoute(
                'update_stock_hattaa',
                [
                    'exeption' => $message,
                    'type' => 'success',
                ]
            );
     
        
    }
}