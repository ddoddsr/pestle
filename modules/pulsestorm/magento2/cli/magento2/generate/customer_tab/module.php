<?php
namespace Pulsestorm\Magento2\Cli\Magento2\Generate\CustomerTab;
use function Pulsestorm\Pestle\Importer\pestle_import;
pestle_import('Pulsestorm\Pestle\Library\output');
pestle_import('Pulsestorm\Pestle\Library\input');
pestle_import('Pulsestorm\Magento2\Cli\Generate\Menu\choseMenuFromTop');
pestle_import('Pulsestorm\Magento2\Cli\Generate\Menu\exported_pestle_cli');
pestle_import('Pulsestorm\Magento2\Cli\Xml_Template\getBlankXmlLayout_handle');
pestle_import('Pulsestorm\Magento2\Cli\Library\getBaseMagentoDir');
pestle_import('Pulsestorm\PhpDotNet\glob_recursive');
pestle_import('Pulsestorm\Magento2\Cli\Library\getModuleInformation');
pestle_import('Pulsestorm\Pestle\Library\writeStringToFile');
pestle_import('Pulsestorm\Magento2\Cli\Xml_Template\getBlankXml');
pestle_import('Pulsestorm\Xml_Library\formatXmlString');

function addAttributesToXml($argv, $xml)
{
    extract($argv);
  //  $xml->body->referenceBlock->block->addAttribute('class' , "Fd\Tab\Block\Adminhtml\Edit\Tab\Compared");
    $xml->body->referenceBlock->block->addAttribute('name' , "customer_edit_tab_compared");
    // $arg1 =  $xml->body->referenceBlock->block->action->arguments->addChild('argument');
    // $arg1->addAttribute('name'  , "tab_label");
    // $arg2=  $xml->body->referenceBlock->block->action->arguments->addChild('argument');
    // $arg2->addAttribute('xsi:type', "string");
    //$xml->body->referenceBlock->block->action->arguments->argument = $title;

    //CLASS  "\Block\Adminhtml\Edit\Tab". "\\" . $class ;
    //NAME    name="customer_edit_tab_'. $name

    return $xml;
}
function loadOrCreateTabXml($path)
{
    if(!file_exists($path))
    {
        //?? $str = '<?xml version="1.0">
        $str = '
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="admin-2columns-left" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceBlock name="customer_form">
            <block >
                <action method="setTabLabel">
                    <arguments>
                        <argument name="tab_label" xsi:type="string" translate="true">Customer CFU</argument>
                    </arguments>
                </action>
            </block>
        </referenceBlock>
    </body>
</page>' ;
        $xml    = simplexml_load_string($str);
        writeStringToFile($path, $xml->asXml());
    }
    $xml    = simplexml_load_file($path);
    return $xml;
}

function generatePageActionClass($moduleInfo)
{
    $pageActionsClassName = 'Tab';
    $contents = 'function Contents() { return ;}';    
    $shortName = 'SHORTNAME';    
    $editUrl = $gridId . '/' . strToLower($shortName) . '/edit';   

    output("Creating: $pageActionsClassName");
    $return   = createClassFile($pageActionsClassName,$contents);             
    return $return;
}
/**
* Generates Customer Tab for Magento Adminhtml 
*
* @command magento2:generate:customer_tab
* @argument module_name Module Name? [Pulsestorm_HelloGenerateTab]
* @argument id Menu Link ID [<$module_name$>::unique_identifier]
* @argument resource ACL Resource [<$id$>]
* @argument title Link Title [My Link Title]
* @argument action Three Segment Action [frontname/index/index]
*/

function pestle_cli($argv)
{
    extract($argv);
    $module_info      = getModuleInformation($module);

    $xmlpath = getModuleInformation($module_name)->folder . '/view/adminhtml/layout/customer_index_edit.xml';
    $xml  = loadOrCreateTabXml($xmlpath);
    $xml  = addAttributesToXml($argv, $xml);
    writeStringToFile($xmlpath, $xml->asXml());
    output("Writing: $xmlpath");
    $classpath = getModuleInformation($module_name)->folder . '/Block/Adminhtml/Edit/Tab/Compared.php';
    $classtext = generatePageActionClass(
        $module_info, 
        $grid_id, 
        $db_id_column);                    
        
    output("Don't forget to add this to your layout XML with <uiComponent name=\"{$argv['grid_id']}\"/> ");        

    writeStringToFile($classpath, $classtext);
    output("Writing: $classpath");
    output("Done.................................................................");

exit;
    
}
