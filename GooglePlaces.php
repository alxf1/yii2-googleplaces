<?php
namespace indicalabs\google;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
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
 	const API_URL = 'https://maps.googleapis.com/maps/api/js?';
	
	public $libraries = 'places';

	
	public $language = 'en-US';

	//'types' => 'establishment', 'componentRestrictions' => ['country' => 'uk']
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

		$this->registerPlugin('placecomplete');
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
    	$className = explode('\\', $this->model->className());
    	$className = strtolower(end($className));
    	
    	$view = $this->getView();
    	GoogleAsset::register($view);
    	$id = $this->options['id'];
    	$options = $this->clientOptions !== false && !empty($this->clientOptions)
    	? Json::encode($this->clientOptions)
    	: '';
    	
   	$view->registerJsFile(self::API_URL . http_build_query([
    			'callback' => 'initAutocomplete',
	   			'key' => Yii::$app->params['googlePlacesAPIKey'],
   				'libraries' => $this->libraries,
    			'language' => $this->language
    	]));
    	
    //	jQuery('.placecomplete-{$this->attribute}').placecomplete({
    //	$js = "jQuery('#$id').google.maps.places.Autocomplete($options);";
    //	$view->registerJs($js, \yii\web\View::POS_READY);
    	$view->registerJs(<<<JS
(function(){
    var input = document.getElementById('{$id}');
	var options = {$options};
	new google.maps.places.Autocomplete(input, options);
})();
JS
    	, \yii\web\View::POS_END);
    	
$view->registerJs(<<<JS
		var placeSearch, autocomplete;
		var componentForm = {
		  street_number: 'short_name',
		  route: 'long_name',
		  locality: 'long_name',
		  administrative_area_level_1: 'short_name',
		  administrative_area_level_2: 'long_name',
		  country: 'long_name',
		  postal_code: 'short_name'
		};

		function initAutocomplete() {
			var input = document.getElementById('{$id}');
			var options = {$options};
		    autocomplete =new google.maps.places.Autocomplete(input, options);
			
		    var location_being_changed;
		    onPlaceChange = function () {
		        location_being_changed = false;
    	};
    
		  // When the user selects an address from the dropdown, populate the address
		  // fields in the form.
		    google.maps.event.addListener(this.autocomplete,'place_changed', fillInAddress);
				google.maps.event.addDomListener(input, 'keydown', function (e) {
				    if (e.keyCode === 13) {
				            e.preventDefault();
				            e.stopPropagation();
 				    }
				});
		}
		
		function fillInAddress() {
		  // Get the place details from the autocomplete object.
		  var place = autocomplete.getPlace();
		  for (var component in componentForm) {
		  var addrElement = document.getElementById("$className-"+component) ;
		    if(addrElement != null){
			    addrElement.value = '';
			    addrElement.disabled = false;
		    }
		  }
		 var addrElement = document.getElementById("$className-location"); 
		 if(addrElement != null){
		 		addrElement.value = '';
		 		addrElement.disabled = false;
		 	}

		  // Get each component of the address from the place details
		  // and fill the corresponding field on the form.
		  for (var i = 0; i < place.address_components.length; i++) {
		    var addressType = place.address_components[i].types[0];
			    if (componentForm[addressType]) {
			      var val = place.address_components[i][componentForm[addressType]];
			      
			      var addrElement = document.getElementById("$className-"+addressType) ;
				  if(addrElement != null){
					     addrElement.value = val;
					     console.log(addrElement.value);
					    }
			    }
		  	}
		   console.log(JSON.stringify(place['geometry']['location']));
		   var addrElement = document.getElementById("$className-location") ;
		   if(addrElement != null){
		   		addrElement.value = JSON.stringify(place['geometry']['location']);
		   		console.log(addrElement.value);
		   		}
		}
		
		// Bias the autocomplete object to the user's geographical location,
		// as supplied by the browser's 'navigator.geolocation' object.
		function geolocate() {
		  if (navigator.geolocation) {
		    navigator.geolocation.getCurrentPosition(function(position) {
		      var geolocation = {
		        lat: position.coords.latitude,
		        lng: position.coords.longitude
		      };
		      var circle = new google.maps.Circle({
		        center: geolocation,
		        radius: position.coords.accuracy
		      });
		      autocomplete.setBounds(circle.getBounds());
		    });
		  }
		}	
JS
		, \yii\web\View::POS_HEAD);
    	
	}
}
