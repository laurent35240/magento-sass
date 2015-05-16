Magento Sass
============

Magento extension for using Sass stylesheet language

## Features
    * Possibility of including sass files that will be automatically converted to css file
    * Use of PhpSass (no need to install sass then) or though sass in command line
    * Possibility of having debug information css files created

## Installation
#### Composer ([magento composer](https://github.com/magento-hackathon/composer-repository) must be installed)

```bash
composer install laurent35240/magento-sass
```

#### Magento Connect
Install this extension though [Magento Connect](http://www.magentocommerce.com/magento-connect/sass.html)

## Configuration
1. Just add your sass file in layout using addCss method. This extension will automatically create css file
in media/sass folder
1. By default this extension use scssphp library, if you want to use sass in command line instead,
change settings in Back Office: System > Configuration > Developer > Sass Settings
1. If you need debug info in css file created, enable in Back Office: System > Configuration > Developer > Sass Settings.
You can use then [FireSass plugin for FireBug][2] for reading easily debug information.

For example you can add this lines in one of your layout files:
```xml
<default>
    <reference name="head">
        <action method="addCss"><stylesheet>css/style.scss</stylesheet></action>
    </reference>
</default>
```

## Requirements
 * PHP 5.3+

## Compatibiity
This extension is compatible with:
 * Magento CE 1.5+
 * Magento EE 1.10+

## Locales
Extension available in:

 * English
 * French

## Bug Reports
If you find a bug, [you can create a ticket][3].

## More informations
Check [Magento Connect Sass page][1] for more details.

## License
Magento Sass extension is licensed under Open Software License (OSL 3.0)

## Changeset
### 1.2.0
switch to leafo/scssphp library

### 1.1.3
phpsass library updated

### 1.1.2
Caching part rewritten for managing it in BO.

### 1.1.1
Missing phpsass library added

### 1.1.0
It is now possible to choose output style in back office.

### 1.0.2
 * Sass available when css merging is used
 * PHPSass updated

### 1.0.1
Conversion of relative urls to absolute urls in css file

### 1.0.0
First version of this extension

[1]: http://www.magentocommerce.com/magento-connect/catalog/product/view/id/14634/
[2]: https://addons.mozilla.org/en-US/firefox/addon/firesass-for-firebug/
[3]: https://github.com/laurent35240/magento-sass/issues
