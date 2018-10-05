<?php
/**
 * PHP Version 7.2
 * @package Polyfony
 * @link https://github.com/SIB-FRANCE/Polyfony
 * @license http://www.gnu.org/licenses/lgpl.txt GNU General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Google;

class Position {
	
	// API url
	private static $_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	// return a GPS position given an address
	public static function address(
		string $address, 
		?array $options = []
	) :?array {
		// new http request
		$request = new \Polyfony\HttpRequest();
		// configure the request
		$request->url(self::$_api_url)->data('address', $address);
		// for each options
		foreach($options as $key => $value) {
			// add it to the request
			$request->data($key, $value);
		}
		// execture the actual request
		$success = $request->get();
		// if the request succeeded
		if($success) {
			// get the response
			$response = $request->getBody();
			// check if the api found results
			if($response['status'] == 'OK') {
				// if gps coordinates are available
				if(isset($response['results'][0]['geometry']['location'])) {
					// return formatted address and position
					return [
						'address'	=>$response['results'][0]['formatted_address'],
						'position'	=>$response['results'][0]['geometry']['location'],
						'response'	=>$response
					];
				}
				// missing position
				else { return null; }
			}
			// api did not found succeed
			else { return null; }
		}
		// the request failed
		else { return null; }
	}

	// return an address given a GPS position
	public static function reverse(
		float $latitude, 
		float $longitude, 
		?array $options = []
	) :?array {
		// new http request
		$request = new \Polyfony\HttpRequest();
		// configure the request
		$request
			->url(self::$_api_url)
			->data('latlng', $latitude . ',' . $longitude);
		// for each options
		foreach($options as $key => $value) {
			// add it to the request
			$request->data($key, $value);
		}
		// actually execute the request
		$success = $request->get();
		// if the request succeeded
		if($success) {
			// get the response
			$response = $request->getBody();
			// check if the api found results
			if($response['status'] == 'OK') {
				// if gps coordinates are available
				if(isset($response['results'][0]['geometry']['location'])) {
					// return formatted address and position
					return [
						'address'	=>$response['results'][0]['formatted_address'],
						'position'	=>$response['results'][0]['geometry']['location'],
						'response'	=>$response
					];
				}
				// missing position
				else { return null; }
			}
			// api did not found succeed
			else { return null; }
		}
		// the request failed
		else { return null; }
	}


}

?>
