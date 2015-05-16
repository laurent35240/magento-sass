<?php
/**
 * Jean Jean Inc.
 *
 *
 * @category   Laurent
 * @package    Laurent_Sass
 * @author     Laurent Clouet <laurent35240@gmail.com>
 * @date       1/21/13
 * @copyright  Copyright (c) 2012 Jean Jean Inc. (http://laurent-clouet.fr/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Laurent_Sass_Model_Config_Style
{
    const STYLE_NESTED     = 'nested';
    const STYLE_EXPANDED   = 'expanded';
    const STYLE_COMPACT    = 'compact';
    const STYLE_COMPRESSED = 'compressed';
    
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray(){
        /** @var $sassHelper Laurent_Sass_Helper_Data */
        $sassHelper = Mage::helper('sass');

        return array(
            array('value' => self::STYLE_NESTED, 'label' => $sassHelper->__('Nested')),
            array('value' => self::STYLE_EXPANDED, 'label' => $sassHelper->__('Expanded')),
            array('value' => self::STYLE_COMPACT, 'label' => $sassHelper->__('Compact')),
            array('value' => self::STYLE_COMPRESSED, 'label' => $sassHelper->__('Compressed')),
        );
    }

    /**
     * Gat array of authorized values
     * @return array
     */
    public function authorizedValues(){
        return array(
            self::STYLE_NESTED,
            self::STYLE_COMPACT,
            self::STYLE_COMPRESSED,
            self::STYLE_EXPANDED,
        );
    }
}
