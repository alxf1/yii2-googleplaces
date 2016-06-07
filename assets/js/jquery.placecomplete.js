// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
      {types: ['geocode']});

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
 // autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }

  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
  setupListeners();
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

function setupListeners() { 
//  google.maps.event.addDomListener(window, 'load', initialize);
    // searchbox is the var for the google places object created on the page
    google.maps.event.addListener(searchbox, 'place_changed', function() {
      var place = searchbox.getPlace();
      if (!place.geometry) {
        // Inform the user that a place was not found and return.
        return;
      }  else {      
        // migrates JSON data from Google to hidden form fields
        console.log('--------->'.place);
        populateResult(place);
      }
  });
}
 
function populateResult(place) {
  // moves JSON data retrieve from Google to hidden form fields
  // so Yii2 can post the data
  $('#profile-street_number').val(JSON.stringify(place['geometry']['street_number']));
  $('#place-route').val(place['route']);
  $('#place-sublocality').val(place['sublocality']);
  $('#place-locality').val(place['locality']);
  $('#place-administrative_area_level_1').val(place['administrative_area_level_1']);
  $('#place-administrative_area_level_2').val(place['administrative_area_level_2']);
 // loadMap(place['geometry']['location'],place['name']);
}
