<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace indicalabs\google;

use yii\web\AssetBundle;
/**
 * Class PlacesAsset
 *
 * @author Thiago Oliveira <thiago.oliveira.gt14@gmail.com>
 */
class TypeAheadAsset extends AssetBundle
{
	public $sourcePath = '@bower/typeahead.js/dist';
	public $js = [
			'typeahead.bundle.js',
	];
	public $depends = [
			'yii\bootstrap\BootstrapPluginAsset'
	];
}