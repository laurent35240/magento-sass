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

require_once 'phpsass/renderers/SassRenderer.php';

class Laurent_Sass_Model_Config_Style
{

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray(){
        /** @var $sassHelper Laurent_Sass_Helper_Data */
        $sassHelper = Mage::helper('sass');

        return array(
            array('value' => SassRenderer::STYLE_NESTED, 'label' => $sassHelper->__('Nested')),
            array('value' => SassRenderer::STYLE_COMPACT, 'label' => $sassHelper->__('Compact')),
            array('value' => SassRenderer::STYLE_COMPRESSED, 'label' => $sassHelper->__('Compressed')),
            array('value' => SassRenderer::STYLE_EXPANDED, 'label' => $sassHelper->__('Expanded')),
        );
    }

    /**
     * Gat array of authorized values
     * @return array
     */
    public function authorizedValues(){
        return array(
            SassRenderer::STYLE_NESTED,
            SassRenderer::STYLE_COMPACT,
            SassRenderer::STYLE_COMPRESSED,
            SassRenderer::STYLE_EXPANDED,
        );
    }
}
