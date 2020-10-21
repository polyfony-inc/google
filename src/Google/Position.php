<?php
/**
 * PHP Version 7.2
 * @package Polyfony
 * @link https://github.com/polyfony-inc/polyfony
 * @license http://www.gnu.org/licenses/lgpl.txt GNU General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Google;
use \Polyfony\Logger as Logger;
use \Polyfony\Exception as Exception;

class Position {
	
	// API url
	private static $_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	// return a GPS position given an address
	public static function address(
		string 	$address, 
		?array 	$options = []
	) :?array {
		// new http request
		$request = new \Polyfony\HttpRequest();
		// configure the request
		$request->url(self::$_api_url)->data('address', $address);
		// add it to the request
		$request->data($options);
		// execture the actual request
		$successful_request = $request->get();
		// get the response
		$response = $request->getBody();
		// if the request succeeded
		if($successful_request) {
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
				else { 
					Logger::warning(
						'No location found for ['.$address.'] (API returned a response, but no location)', 
						$response
					);
					return null; 
				}
			}
			// api did not found succeed
			else { 
				Logger::warning(
					'No location found for ['.$address.'] (API returned an error)', 
					$response['error_message']
				);
				return null; 
			}
		}
		// the request failed
		else {
			Logger::warning(
				'No location found for ['.$address.'] (API didn\'t return a valid HTTP Response)', 
				$response
			);
			return null; 
		}
	}

	// return an address given a GPS position
	public static function reverse(
		float $latitude, 
		float $longitude, 
		?array $options = []
	) :?array {
		// new http request
		$request = new \Polyfony\HttpRequest();
		// assemble latlng
		$latlng = $latitude . ',' . $longitude;
		// configure the request
		$request
			->url(self::$_api_url)
			->data('latlng', $latlng);
		// add it to the request
		$request->data($options);
		// actually execute the request
		$successful_request = $request->get();
		// if the request succeeded
		if($successful_request) {
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
				else { 
					Logger::warning(
						'No location found for ['.$latlng.'] (API returned a response, but no location)', 
						$response
					);
					return null; 
				}
			}
			// api did not found succeed
			else { 
				Logger::warning(
					'No location found for ['.$latlng.'] (API returned an error)', 
					$response['error_message']
				);
				return null; 
			}
		}
		// the request failed
		else {
			Logger::warning(
				'No location found for ['.$latlng.'] (API didn\'t return a valid HTTP Response)', 
				$response
			);
			return null; 
		}
	}


}

?>
