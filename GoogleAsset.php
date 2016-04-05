<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace indicalabs\google;

use yii\web\AssetBundle;

/**
 * @author Venu Narukulla. Venu <venu.narukulla@gmail.com>
 * @since 2.0
 */
class GoogleAsset extends AssetBundle
{
	public $sourcePath = __DIR__.'/assets';
	public $js = [
		'js/jquery.placecomplete.js',
		//'js/typeahead.js',
	//	'js/typeahead.min.js',
	];
	public $css = [
			'css/jquery.placecomplete.css',
			'css/places.css',
			//'css/typeahead.js-bootstrap.less',
	];
	public $depends = [
		'yii\jui\JuiAsset',
		'yii\web\JqueryAsset',
	];
}
