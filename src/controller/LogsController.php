<?php
namespace Anis\Stockmanagement\Controller;
require_once __DIR__ . '/../../vendor/autoload.php';
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LogsController extends FrameworkBundleAdminController 
{
    public function addLog($message, $clearFile = false) {   
      
        $logFile = realpath(dirname(__FILE__) . '/../logs/log.text');
      
        if ($clearFile) {
            if (file_put_contents($logFile, '')) {
                
               
                return $this->redirectToRoute(
                    'getLogContent',
                    [
                        'exeption' => "Failed to clear the log file $logFile",
                        'type' => 'danger',
                    ]
                );
            }else{
                
                return $this->redirectToRoute(
                    'getLogContent',
                    [
                        'exeption' => "logs has been deleted from file $logFile",
                        'type' => 'success',
                    ]
                );
            }
        } else {
            if (!file_put_contents($logFile, $message, FILE_APPEND)) {
                error_log("Failed to write to log file $logFile");
            }
        }
    }

    public function getLogContent(Request $request) {
        
        $logFile = realpath(dirname(__FILE__) . '/../logs/log.text');
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            if ($content === false) {
                
               
                return $this->redirectToRoute(
                    'getLogContent',
                    [
                        'exeption' => "Failed to read the log file $logFile",
                        'type' => 'danger',
                    ]
                );
                
            }
            $logEntries = explode("\n", $content);
            
            $parsedEntries = [];

            foreach ($logEntries as $entry) {
                if (trim($entry) !== '') {
                    $date = substr($entry, 0, 19); // Extract the date part
                    $message = substr($entry, 20); // Extract the message part
                    $parsedEntries[] = ['date' => $date, 'message' => $message];
                }
            }
            $page = $request->query->getInt('page', 1);
            $pageSize = 20;
            $totalLogs = count($parsedEntries);
            $totalPages = ceil($totalLogs / $pageSize);
            $start = ($page - 1) * $pageSize;
            $paginatedEntries = array_slice($parsedEntries, $start, $pageSize);
            
            return $this->render(
                '@Modules/stockmanagement/views/templates/twig/logs.html.twig',
                [
                    'enableSidebar' => true,
                    'logs' => $paginatedEntries,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                ]
            );

        } else {
            
            
            return $this->redirectToRoute(
                'getLogContent',
                [
                    'exeption' => "Log file $logFile does not exist",
                    'type' => 'danger',
                ]
            );
        }
    }
   
}
