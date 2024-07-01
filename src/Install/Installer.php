<?php
declare(strict_type=1);
namespace Anis\Stockmanagement\Install;
use Tab;
use Language;
use Module;
use Db;
class Installer{
    private $tabs =[
        [
            'class_name'=>"StockManagement",
            'parent_class_name'=>"AdminCatalog",
            'name'=>"Virtual Stock Management",
            'icon'=>"",
            'wording'=>"Virtual Stock Management",
            'wording_domain'=>"Modules.Stockmanagement.Admin",
        ]
    ];
    public function install(Module $module){
        if(!$this->registerHooks($module)){
            return false;
        }
        if(!$this->installTab($module)){
            return false;
        }
        if(!$this->installDatabase($module)){
            return false;
        }
        return true;
    }
    public function unInstall():bool
    {
       return  $this->unInstallDatabase() && $this->unInstallTab();
    }
    public function registerHooks(Module $module)
    {
        $hooks = [
            'moduleRoutes',
        ];
        return (bool) $module->registerHook($hooks); 
    }
    protected function installTab(Module $module):bool
    {
        $languages = Language::getLanguages();
        foreach ($this->tabs as $t)
       { 
        $exist = Tab::getIdFromClassName($t['class_name']);
        if(!$exist){
            $tab = new Tab();
            $tab->active = true;
            $tab->enabled = true;
            $tab->module = $module->name;
            $tab->class_name = $t['class_name'];
            $tab->id_parent = (int)Tab::getInstanceFromClassName($t['parent_class_name'])->id;
            foreach($languages as $language){
                $tab->name[$language['id_lang']] = $t['name'];

            }
            $tab->icon = $t['icon'];
            $tab->wording = $t['wording'];
            $tab->wording_domain = $t['wording_domain'];
            $tab->save();

        }
    
        }
        return true;
    }
    protected function unInstallTab():bool
    {
        foreach ($this->tabs as $t){
            $id = Tab::getIdFromClassName($t['class_name']);
            if($id){
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        return true;
    }
    public  function installDatabase():bool 
    {
      return   $this->executeQueries(Database::installQueries());
    }
    public  function unInstallDatabase():bool 
    {
      return   $this->executeQueries(Database::uninstallQueries());
    }
    public  function executeQueries(array $queries){
        if(empty($queries)) return true;
        foreach($queries as $query){
            if(!Db::getInstance()->execute($query)){
               return  false;
            }
        }
        return true;
    }
}