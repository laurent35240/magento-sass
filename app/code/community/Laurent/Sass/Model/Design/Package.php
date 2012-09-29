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
        /** @var $sassHelper Laurent_Sass_Helper_Data */
        $sassHelper = Mage::helper('sass');
        $fileExtension = $sassHelper->getFileExtension($file);

        if($fileExtension == 'scss' || $fileExtension == 'sass'){
            if (empty($params['_type'])) {
                $params['_type'] = 'skin';
            }
            $targetFilename = Mage::getBaseDir('media') . DS . 'sass' . DS . md5($file) . '.css';
            $sourceFilename = $this->getFilename($file, $params);

            try{
                $sassHelper->convertToCss($sourceFilename, $targetFilename);

                $skinUrl = str_replace(Mage::getBaseDir('media') . DS, '', $targetFilename);
                $skinUrl = str_replace('\\', '/', $skinUrl);
                $skinUrl = Mage::getBaseUrl('media', isset($params['_secure']) ? (bool)$params['_secure'] : null) . $skinUrl;
            }
            catch(Exception $e){
                Mage::logException($e);
                $skinUrl = '';
            }

        }
        else{
            $skinUrl = parent::getSkinUrl($file, $params);
        }

        return $skinUrl;
    }


}
