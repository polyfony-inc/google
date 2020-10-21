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
use \Curl\Curl;

class Position {
	
	// API url
	private static $_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	// return a GPS position given an address
	public static function address(
		string 	$address, 
		?array 	$options = []
	) :?array {
		// new http request
		$request = new Curl;
		// send the request
		$request->get(
			self::$_api_url, 
			array_merge(
				['address'=>$address],
				$options
			)
		);
		// if the request failed
		if($request->error) {
			// log the error
			Logger::warning(
				'No location found for ['.$address.'] (API didn\'t return a valid HTTP Response)', 
				$request
			);
			// do not return a position
			return null; 
		}
		else {
			// check if the api found results
			if($request->getHttpStatusCode() == 200) {
				// cast the response to array
				$response = json_decode(
					json_encode(
						$request->getResponse()
					), true
				);
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
					$request->getErrorMessage()
				);
				return null; 
			}

		}

	}

	// return an address given a GPS position
	public static function reverse(
		float $latitude, 
		float $longitude, 
		?array $options = []
	) :?array {
		// new http request
		$request = new Curl;
		// send the request
		$request->get(
			self::$_api_url, 
			array_merge(
				['latlng'=>$latitude . ',' . $longitude],
				$options
			)
		);

		// if the request failed
		if($request->error) {
			// log the error
			Logger::warning(
				'No location found for ['.json_encode([$latitude,$longitude]).'] (API didn\'t return a valid HTTP Response)', 
				$request
			);
			// do not return a position
			return null; 
		}
		else {
			// check if the api found results
			if($request->getHttpStatusCode() == 200) {
				// cast the response to array
				$response = json_decode(
					json_encode(
						$request->getResponse()
					), true
				);
				
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
						'No location found for ['.json_encode([$latitude,$longitude]).'] (API returned a response, but no location)', 
						$response
					);
					return null; 
				}


			}
			// api did encountered an error
			else { 
				Logger::warning(
					'No location found for ['.json_encode([$latitude,$longitude]).'] (API returned an error)', 
					$request->getErrorMessage()
				);
				return null; 
			}

		}

	}


}

?>