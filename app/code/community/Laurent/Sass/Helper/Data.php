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

require_once 'richthegeek/phpsass/SassParser.php';

class Laurent_Sass_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Convert a sass file to a css file and put the content in $targetFilename
     * This method actually does conversion if something change in sass file or in configuration
     * @param string $sourceFilename
     * @param string $targetFilename
     * @throws Exception
     */
    public function convertToCss($sourceFilename, $targetFilename){
        //Conversion needed only if sass file is newer than converted css one
        $sassFileModifTime = filemtime($sourceFilename);

        $cacheData = array(
            'source_filename'   => $sourceFilename,
            'target_filename'   => $targetFilename,
            'source_modif_time' => $sassFileModifTime,
            'config'            => $this->_getConfig(),
        );
        $cacheDataSerialized = serialize($cacheData);

        $cacheKey = 'sass_' . $sourceFilename . '_' . $targetFilename;
        /** @var $cacheModel Mage_Core_Model_Cache */
        $cacheModel = Mage::getSingleton('core/cache');
        $cachedString = $cacheModel->load($cacheKey);

        if($cachedString != $cacheDataSerialized){
            $this->createNewCss($sourceFilename, $targetFilename);

            $cacheModel->save($cacheDataSerialized, $cacheKey);
        }
    }

    /**
     * Create a new css file based on sass file
     * @param string $sourceFilename
     * @param string $targetFilename
     * @throws Exception
     */
    public function createNewCss($sourceFilename, $targetFilename){
        $config = $this->_getConfig();
        $targetDir = dirname($targetFilename);
        $this->_createDir($targetDir);
        $this->_createDir($config['cache_dir']);

        if($config['use_ruby']){
            $options = '--cache-location ' . $config['cache_dir'];
            if($config['debug']){
                $options .= ' --debug-info --line-numbers';
            }
            $command = $config['sass_command'] . ' ' . $options . ' ' . $sourceFilename .':' . $targetFilename;
            $execResult = exec($command, $output);
            if($execResult != ''){
                throw new Exception("Error while processing sass file with command '$command':\n" . implode("\n", $output));
            }
        }
        else{
            //Using PhpSass
            $sassOptions = array(
                'style'         => SassRenderer::STYLE_NESTED,
                'syntax'        => $this->getFileExtension($sourceFilename),
                'debug'         => $config['debug'],
                'debug_info'    => $config['debug'],
                'line_numbers'  => $config['debug'],
                'callbacks'     => array(
                    'warn'  => array(__CLASS__, 'logWarning'),
                    'debug' => array(__CLASS__, 'logDebug'),
                ),
            );
            $sassParser = new SassParser($sassOptions);
            $cssContent = $sassParser->toCss($sourceFilename);
            file_put_contents($targetFilename, $cssContent);
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
        return array(
            'use_ruby'      => (bool) Mage::getStoreConfig('dev/sass/use_ruby'),
            'sass_command'  => Mage::getStoreConfig('dev/sass/ruby_sass_command'),
            'debug'         => (bool) Mage::getStoreConfig('dev/sass/debug'),
            'cache_dir'     => Mage::getBaseDir('cache') . DS . 'sass',
        );
    }
}
