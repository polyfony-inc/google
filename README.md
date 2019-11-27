## Google Helpers


### Google\Position

```php
new Google\Position(
	bool $throw_exceptions_on_error = true,
	bool $log_errors = true,
	string $api_key = Config::get('google','api_key')
)
```

#### Geocoding

```php
(new Google\Position)
	->setAddress('Place Charges de Gaulle, 75008 Paris, France')
	->getPosition(); // array
```

**More options**
```php
->getLatitude() // float
->getLongitude() // float
```

#### Reverse geocoding

```php
(new Google\Position)
	->setPosition(
		48.8737917, 
		2.2950275
	)
	->getAddress(); // string
```

**More options**
```php
->getStreet() // string
->getStreetNumber() // string
->getZipCode()  // string
->getCity() // string
->getCountry() // string
```

### Google\Photo

* Retrieve a photo url from streetview
```php
$photo_url = (new \Google\Photo)
//	->position($lat,$lnt)
	->option(['key'=>'YourGoogleApiKeyHere'])
	->address('Some normal address')
	->size(500,500)
	->url();
```

### Google\Map

* Retrieve a static map url with a marker
```php
$map_url = (new \Google\Map)
	->option(['key'=>'YourGoogleApiKeyHere'])
	->center($lat, $lng);
	->zoom(7)
	->retina(true)
	->marker($lat, $lng)
	->size(600,600)
	->url();
```
