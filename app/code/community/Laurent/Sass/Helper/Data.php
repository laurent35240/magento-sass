<?php
/**
 * Jean Jean Inc.
 *
 *
 * @category   Laurent
 * @package    Laurent_Helper
 * @author     Laurent Clouet <laurent35240@gmail.com>
 * @date       9/29/12
 * @copyright  Copyright (c) 2012 Jean Jean Inc. (http://laurent-clouet.fr/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'scssphp/scss.inc.php';

class Laurent_Sass_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Convert a sass file to a css file and put the content in $targetFilename
     * This method actually does conversion if something change in sass file or in configuration
     * May apply callback to converted file. Callback gets parameters:
     * (<source filename>, <target filename>)
     * @param string $sourceFilename
     * @param string $targetFilename
     * @param callback $afterConvertCallback
     * @throws Exception
     */
    public function convertToCss($sourceFilename, $targetFilename, $afterConvertCallback = null)
    {
        $this->createNewCss($sourceFilename, $targetFilename);

        if($afterConvertCallback && is_callable($afterConvertCallback)){
            call_user_func($afterConvertCallback, $sourceFilename, $targetFilename);
        }
    }

    /**
     * Create a new css file based on sass file
     * @param string $sourceFilePath
     * @param string $targetFilenamePath
     * @throws Exception
     */
    public function createNewCss($sourceFilePath, $targetFilenamePath){
        $config = $this->_getConfig();
        $targetDir = dirname($targetFilenamePath);
        $this->_createDir($targetDir);
        $this->_createDir($config['cache_dir']);

        if($config['use_ruby']){
            $options = '--cache-location ' . $config['cache_dir'] . ' --style ' . $config['output_style'];
            if($config['debug']){
                $options .= ' --debug-info --line-numbers';
            }
            $command = $config['sass_command'] . ' ' . $options . ' ' . $sourceFilePath .':' . $targetFilenamePath;
            $execResult = exec($command, $output);
            if($execResult != ''){
                throw new Exception("Error while processing sass file with command '$command':\n" . implode("\n", $output));
            }
        } else {
            $compiler = new \Leafo\ScssPhp\Compiler();
            switch ($config['output_style']) {
                case Laurent_Sass_Model_Config_Style::STYLE_COMPACT:
                default:
                    $formatter = 'scss_formatter_crunched';
                    break;
                case Laurent_Sass_Model_Config_Style::STYLE_NESTED:
                    $formatter = 'scss_formatter_nested';
                    break;
                case Laurent_Sass_Model_Config_Style::STYLE_COMPRESSED:
                    $formatter = 'scss_formatter_compressed';
                    break;
                case Laurent_Sass_Model_Config_Style::STYLE_EXPANDED:
                    $formatter = 'scss_formatter';
                    break;
            }
            $compiler->setFormatter($formatter);
            $compiler->setImportPaths(array (
                dirname($sourceFilePath),
                Mage::getBaseDir('lib') . '/scssphp/stylesheets',
            ));

            file_put_contents($targetFilenamePath, $compiler->compile(sprintf('@import "%s"', basename($sourceFilePath))));
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getFileExtension($filename){
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * @param string $message
     */
    public function logWarning($message){
        Mage::log($message, Zend_Log::WARN);
    }

    /**
     * @param string $message
     */
    public function logDebug($message){
        Mage::log($message, Zend_Log::DEBUG);
    }

    /**
     * @param string $dirName
     */
    private function _createDir($dirName){
        if(!file_exists($dirName)){
            mkdir($dirName, 0775, true);
        }
    }

    private function _getConfig(){
        //Getting output style
        $outputStyle = Mage::getStoreConfig('dev/sass/output_style');
        /** @var $configStyleModel Laurent_Sass_Model_Config_Style */
        $configStyleModel = Mage::getModel('sass/config_style');
        $authorizedOutputStyles = $configStyleModel->authorizedValues();
        if(!in_array($outputStyle, $authorizedOutputStyles)){
            $outputStyle = Laurent_Sass_Model_Config_Style::STYLE_NESTED;
        }

        return array(
            'use_ruby'      => (bool) Mage::getStoreConfig('dev/sass/use_ruby'),
            'sass_command'  => Mage::getStoreConfig('dev/sass/ruby_sass_command'),
            'debug'         => (bool) Mage::getStoreConfig('dev/sass/debug'),
            'cache_dir'     => Mage::getBaseDir('cache') . DS . 'sass',
            'output_style'  => $outputStyle,
        );
    }
}
