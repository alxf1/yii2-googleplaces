<?php
namespace indicalabs\google;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * @author Bryan Jayson Tan <admin@bryantan.info>
 * @link http://bryantan.info
 * @date 3/1/13
 * @time 3:06 PM
 *
 * make sure that you have CURL installed
 */
class GooglePlaces extends InputWidget
{
	const API_URL = '//maps.googleapis.com/maps/api/js?';
	public $libraries = 'places';
	public $sensor = true;
	
	public $language = 'en-US';

	public $options = [];
	public $clientOptions = [];
	
	public function init()
	{
		parent::init();
	
		$this->clientOptions = ArrayHelper::merge([
				'class' => 'form-control',
		], $this->clientOptions);
		 
		$this->options = ArrayHelper::merge($this->options,$this->clientOptions);
	}
	
	/**
	 * Renders the widget.
	 */
	public function run(){

		$this->registerPlugin('googlePlaces');
		if ($this->hasModel()) {
			return Html::activeTextInput($this->model, $this->attribute, $this->options);
		} else {
			return Html::textInput($this->name, $this->value, $this->options);
		}
	}

	/**
	 * Registers the needed JavaScript.
	 */
	protected function registerPlugin()
    {
    	$view = $this->getView();
    	GoogleAsset::register($view);
    	$id = $this->options['id'];
    	$options = $this->clientOptions !== false && !empty($this->clientOptions)
    	? Json::encode($this->clientOptions)
    	: '';
    	
    	$view->registerJsFile(self::API_URL . http_build_query([
    			'libraries' => $this->libraries,
    			'sensor' => $this->sensor ? 'true' : 'false',
    			'language' => $this->language
    	]));
    	
    //	jQuery('.placecomplete-{$this->attribute}').placecomplete({
    //	$js = "jQuery('#$id').google.maps.places.Autocomplete($options);";
    //	$view->registerJs($js, \yii\web\View::POS_READY);
    	$view->registerJs(<<<JS
(function(){
    var input = document.getElementById('{$elementId}');
    var options = {$scriptOptions};
    new google.maps.places.Autocomplete(input, options);
})();
JS
    	, \yii\web\View::POS_READY);
    	
	}
}
