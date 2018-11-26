## Google Helpers


## Examples, availables in the Demo bundle of Polyfony

![Usage examples](https://github.com/sib-retail/polyfony-google/blob/master/doc/examples.png)


#### Google\Position

* Geocoding

```php
\Google\Position::address(
	'Paris',
	[
		'key'=>'YourGoogleApiKeyHere'
	]
);
```

* Reverse geocoding

```php
\Google\Position::reverse(
	48.856614,
	2.3522219,
	[
		'key'=>'YourGoogleApiKeyHere'
	]
);
```

#### Google\Photo

* Retrieve a photo url from streetview
```php
$photo_url = (new \Google\Photo)
//	->position($lat,$lnt)
	->option(['key'=>'YourGoogleApiKeyHere'])
	->address('Some normal address')
	->size(500,500)
	->url();
```

#### Google\Map

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
