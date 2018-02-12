## Google Helpers


## Examples, availables in the Demo bundle of Polyfony

![Usage examples](/doc/examples.jpg?raw=true)


#### Google\Position

* Geocoding

```php
\Google\Position::address('Paris')
```

* Reverse geocoding

```php
\Google\Position::reverse(48.856614,2.3522219)
```

#### Google\Photo

* Retrieve a photo from streetview
```php
$photo = new \Google\Photo();
$photo_url = $photo
//	->position($lat,$lnt)
	->address('Some normal address')
	->size(500,500)
	->url();
```

#### Google\Map

* Retrieve a static map with a marker
```php
$map = new \Google\Map();
$map_url = $map
	->center($lat,$lng);
	->zoom(7)
	->retina(true)
	->marker($lat,$lng)
	->size(600,600)
	->url();
```

#### Google\QRCode

* Generate a QRCode url

```php
Google\QRCode::url($data, $size)
```

