<?php
/**
 * Jean Jean Inc.
 *
 *
 * @category   Laurent
 * @package    Laurent_Sass
 * @author     Laurent Clouet <laurent35240@gmail.com>
 * @date       9/29/12
 * @copyright  Copyright (c) 2012 Jean Jean Inc. (http://laurent-clouet.fr/)
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
        $skinUrl = '';

        if ($this->_isSassFile($file)) {
            if (empty($params['_type'])) {
                $params['_type'] = 'skin';
            }

            $targetFilename = $this->getFilename($file, $params);

            if ($targetFilename) {
                $skinUrl = str_replace(Mage::getBaseDir('media') . DS, '', $targetFilename);
                $skinUrl = str_replace('\\', '/', $skinUrl);
                $skinUrl = Mage::getBaseUrl('media', isset($params['_secure']) ? (bool)$params['_secure'] : null) . $skinUrl;
            }

        }
        else {
            $skinUrl = parent::getSkinUrl($file, $params);
        }

        return $skinUrl;
    }

    /**
     * Filename of css file or compiled css from sass file
     * This method is important for using css merging feature
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
        $filename = parent::getFilename($file, $params);;

        if ($this->_isSassFile($file)) {
            try {
                if(Mage::app()->useCache('sass')) {
                    $cacheKey = $this->getCacheKey($filename);
                    $compiledFilename = Mage::app()->loadCache($cacheKey);
                    if(!$compiledFilename) {
                        $compiledFilename = $this->convertToCss($filename);
                        Mage::app()->saveCache($compiledFilename, $cacheKey, array('sass'), 86400);
                    }
                }
                else {
                    $compiledFilename = $this->convertToCss($filename);
                }
                $filename = $compiledFilename;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $filename = '';
            }
        }

        return $filename;
    }

    /**
     * Convert a sass file to css file and return css file name
     * @param $filename
     * @return string compiled filename
     */
    public function convertToCss($filename)
    {
        $sassHelper = Mage::helper('sass');
        $compiledFilename = Mage::getBaseDir('media') . DS . 'sass' . DS . md5($filename) . '.css';
        $sassHelper->convertToCss($filename, $compiledFilename, array($this, 'afterConvertToCss'));

        return $compiledFilename;
    }

    /**
     * Method called after conversion from scss to css in order to change relative urls in css
     * @param $sourceFilename
     * @param $targetFilename
     */
    public function afterConvertToCss($sourceFilename, $targetFilename)
    {
        $this->_setCallbackFileDir($sourceFilename);
        $targetFileContent = file_get_contents($targetFilename);
        $cssUrl = '/url\\(\\s*(?!data:)([^\\)\\s]+)\\s*\\)?/';
        $targetFileContent = preg_replace_callback($cssUrl, array($this, '_cssMergerUrlCallback'), $targetFileContent);

        file_put_contents($targetFilename, $targetFileContent);
    }

    /**
     * @param $sourceFilename
     * @return string
     */
    public function getCacheKey($sourceFilename)
    {
        //Checking if we are in secure area
        $store = Mage::app()->getStore();
        if ($store->isAdmin()) {
            $secure = $store->isAdminUrlSecure();
        } else {
            $secure = $store->isFrontUrlSecure() && Mage::app()->getRequest()->isSecure();
        }

        $cacheKeyInfo = array(
            $sourceFilename,
            $store->getId(),
            $secure,
        );
        $key = implode('|', $cacheKeyInfo);
        $key = sha1($key);

        return $key;
    }

    /**
     * Check if provided file is a sass file (based on its extension)
     * @param $file
     * @return bool
     */
    protected function _isSassFile($file){
        /** @var $sassHelper Laurent_Sass_Helper_Data */
        $sassHelper = Mage::helper('sass');
        $fileExtension = $sassHelper->getFileExtension($file);

        return ($fileExtension == 'scss' || $fileExtension == 'sass');
    }

}
