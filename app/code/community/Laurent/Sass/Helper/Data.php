<?php
/**
 * Jean Jean Inc.
 *
 *
 * @category   Copyright (c) 2012 Jean Jean Inc. (http://laurent-clouet.fr/)
 * @package    Laurent_Helper
 * @author     Laurent Clouet <laurent35240@gmail.com>
 * @date       9/29/12
 * @copyright  ${copyright}
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'richthegeek/phpsass/SassParser.php';

class Laurent_Sass_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Convert a sass file to a css file and put the content in $targetFilename
     * @param string $sourceFilename
     * @param string $targetFilename
     * @throws Exception
     */
    public function convertToCss($sourceFilename, $targetFilename){
        if (!file_exists(dirname($targetFilename))) {
            mkdir(dirname($targetFilename), 0775, true);
        }

        $debug = (bool) Mage::getStoreConfig('dev/sass/debug');

        if(Mage::getStoreConfig('dev/sass/use_ruby')){
            $sassExec = Mage::getStoreConfig('dev/sass/ruby_sass_command');
            $options = $debug ? ' --debug-info --line-numbers' : '';
            $command = $sassExec . $options . ' ' . $sourceFilename .':' . $targetFilename;
            exec($command, $output);
            if($output != ''){
                throw new Exception("Error while processing sass file with command '$command': $output");
            }
        }
        else{
            //Using PhpSass
            $sassOptions = array(
                'style'         => SassRenderer::STYLE_NESTED,
                'syntax'        => $this->getFileExtension($sourceFilename),
                'debug'         => true,
                'debug_info'    => $debug,
                'line_numbers'  => $debug,
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
}
