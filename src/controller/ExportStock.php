<?php
namespace Anis\Stockmanagement\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportStock extends FrameworkBundleAdminController
{
    public function export_stock(){
        if(!isset($_GET['type'])){
            return $this->redirectToRoute(
                'virtualstock'
            );
        }
        if($_GET['type']==1){

        }elseif($_GET['type']== 2){
            $tableName = 'stock_hattaa';

    // Connexion à la base de données et récupération des données
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . $tableName;
    $results = \Db::getInstance()->executeS($sql);

    // Créer un nouveau fichier Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter les en-têtes des colonnes
    $columnNames = array_keys($results[0]);
    $columnIndex = 1;
    foreach ($columnNames as $columnName) {
        $sheet->setCellValueByColumnAndRow($columnIndex, 1, $columnName);
        $columnIndex++;
    }

    // Ajouter les données de la table
    $rowIndex = 2;
    foreach ($results as $row) {
        $columnIndex = 1;
        foreach ($row as $cell) {
            $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $cell);
            $columnIndex++;
        }
        $rowIndex++;
    }

    // Créer le fichier Excel
    $writer = new Xlsx($spreadsheet);
    
    // Définir le nom du fichier
    $fileName = 'export_' . $tableName . '_' . date('Y-m-d_H-i-s') . '.xlsx';
    $filePath = _PS_CACHE_DIR_ . $fileName;

    // Enregistrer le fichier dans le répertoire cache de PrestaShop
    $writer->save($filePath);

    // Définir les en-têtes pour le téléchargement du fichier
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Cache-Control: max-age=0');

    // Envoyer le fichier au navigateur
    readfile($filePath);

    // Supprimer le fichier après téléchargement
    unlink($filePath);
    
    exit;

        }else{
            return $this->redirectToRoute(
                'virtualstock'
            );
        }
    }
}