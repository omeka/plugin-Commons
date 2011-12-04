<?php

function cc_license_link($license)
{
    $licenseData = array(
        'cc-0' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/cc-0.png',
        	'link'=>'http://creativecommons.org/licenses/cc-zero/3.0',
        	'short_label'=>'CC-0',
        	'long_label'=>'Public Domain Dedication'
        ),
    	
    	'by' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by.png',
        	'link'=>'http://creativecommons.org/licenses/by/3.0',
        	'short_label'=>'BY',
        	'long_label'=>'Attribution'
        ),
        'by-nd' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by-nd.png',
        	'link'=>'http://creativecommons.org/licenses/by-nd/3.0',
        	'short_label'=>'BY-ND',
        	'long_label'=>'Attribution-NoDerivs'
        ),
        'by-nc-sa' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by-nc-sa.png',
        	'link'=>'http://creativecommons.org/licenses/by-nc-sa/3.0',
        	'short_label'=>'BY-NC-SA',
        	'long_label'=>'Attribution-NonCommercial-ShareAlike'
        ),
        'by-sa' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by-sa.png',
        	'link'=>'http://creativecommons.org/licenses/by-sa/3.0',
        	'short_label'=>'BY-SA',
        	'long_label'=>'Attribution-ShareAlike'
        ),
        'by-nc' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by-nc.png',
        	'link'=>'http://creativecommons.org/licenses/by-nc/3.0',
        	'short_label'=>'BY-NC',
        	'long_label'=>'Attribution-NonCommercial'
        ),
        'by-nc-nd' => array(
        	'button'=> WEB_ROOT . '/plugins/Commons/views/shared/images/by-nc-nd.png',
        	'link'=>'http://creativecommons.org/licenses/by-nc-nd/3.0',
        	'short_label'=>'BY-NC-ND',
        	'long_label'=>'Attribution-NonCommercial-NoDerivs'
        ),
    );
    
    $html = "<a href='" . $licenseData[$license]['link'] . "'>";
    $html .= "<img class='commons-cc' src='" . $licenseData[$license]['button'] . "'/>";
    $html .= $licenseData[$license]['short_label'];
    $html .= "</a>";
    return $html;
    
}