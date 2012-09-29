<?php
/**
 * Jean Jean Inc.
 *
 *
 * @category   Copyright (c) 2012 Jean Jean Inc. (http://laurent-clouet.fr/)
 * @package    Laurent_Sass
 * @author     Laurent Clouet <laurent35240@gmail.com>
 * @date       9/29/12
 * @copyright  ${copyright}
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Laurent_Sass_Model_Design_Package extends Mage_Core_Model_Design_Package
{
    /**
     * Get skin file url
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getSkinUrl($file = null, array $params = array())
    {
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if($fileExtension == 'scss' || $fileExtension == 'sass'){
            //Compiling and caching sass file
            $targetFilename = Mage::getBaseDir('media') . DS . 'sass' . DS . md5($file) . '.css';

            if (!file_exists(dirname($targetFilename))) {
                mkdir(dirname($targetFilename), 0775, true);
            }

            if (empty($params['_type'])) {
                $params['_type'] = 'skin';
            }

            if(Mage::getStoreConfig('dev/sass/use_ruby')){
                $sassExec = Mage::getStoreConfig('dev/sass/ruby_sass_command');
                $command = $sassExec . ' ' . $this->getFilename($file, $params) .':' . $targetFilename;
                exec($command, $output);
            }
            else{
                //Using PhpSass
            }

            $skinUrl = str_replace(Mage::getBaseDir('media') . DS, '', $targetFilename);
            $skinUrl = str_replace('\\', '/', $skinUrl);
            $skinUrl = Mage::getBaseUrl('media', isset($params['_secure']) ? (bool)$params['_secure'] : null) . $skinUrl;

        }
        else{
            $skinUrl = parent::getSkinUrl($file, $params);
        }

        return $skinUrl;
    }


}
