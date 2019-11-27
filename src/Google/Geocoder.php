<?php
/**
 * PHP Version 7.3
 * @package Polyfony
 * @link https://github.com/SIB-FRANCE/Polyfony
 * @license http://www.gnu.org/licenses/lgpl.txt GNU General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Google;

use \Polyfony\Logger as Logger;
use \Polyfony\Exception as Exception;
use \Polyfony\Config as Config;
use \Curl\Curl;

class Geocoder {
	
	// API url
	const API_BASE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

	// options/configuration
	private $throwExceptionsOnErrors;
	private $logErrors;
	private $apiKey;
	private $apiResult;
	private $apiQuery;
	private $error;
	

	// address in full (consolidated)
	private $address; 		// Read and Write

	// address, in separate components
	private $streetNumber; 	// Read only
	private $street;		// Read only
	private $zipCode;		// Read only
	private $city;			// Read only
	private $department;	// Read only
	private $region;		// Read only
	private $country;		// Read only

	// the full position (lat,lng)
	private $position; 		// Read and Write

	// position, in separate components
	private $latitude;		// Read only
	private $longitude;		// Read only

	

	public function __construct(
		bool $throw_exceptions_on_errors = true, 
		bool $log_errors = true,
		string $api_key = null
	) {

		// if we wan't to throw exception in case of errors
		$this->throwExceptionsOnErrors = 
			$throw_exceptions_on_errors;

		// if we want to log error to the internal logging engine
		$this->logErrors = 
			$log_errors;

		// if we have key provided, otherwise fish it from the configuration
		$this->apiKey = $api_key ? 
			$api_key : 
			Config::get('google','api_key');

		return $this;

	}

	// set an address 
	public function setAddress(
		string $address
	) :self {
		$this->address = $address;
		return $this;
	}

	// get an address from a position (GPS coordinates)
	public function getAddress() :string {
		$this->requireApiResult();
		return $this->address;
	}
	
	public function getStreet() :?string {
		$this->requireApiResult();
		return $this->street;
	}
	public function getStreetNumber() :?string {
		$this->requireApiResult();
		return $this->streetNumber;
	}
	public function getZipCode() :?string {
		$this->requireApiResult();
		return $this->zipCode;
	}
	public function getCity() :?string {
		$this->requireApiResult();
		return $this->city;
	}
	public function getDepartment() :?string {
		$this->requireApiResult();
		return $this->department;
	}
	public function getRegion() :?string {
		$this->requireApiResult();
		return $this->region;
	}
	public function getCountry() :?string {
		$this->requireApiResult();
		return $this->country;
	}

	// set a position (GPS coordinates)
	public function setPosition(
		float $latitude, 
		float $longitude
	) :self {
		$this->position = [
			$latitude, 
			$longitude
		];
		return $this;
	}

	// get a position (GPS coordinates) from an address
	public function getPosition() :array {
		$this->requireApiResult();
		return $this->position;
	}

	public function getLatitude() :float {
		$this->requireApiResult();
		return $this->latitude;
	}
	public function getLongitude() :float {
		$this->requireApiResult();
		return $this->longitude;
	}

	// get the last error
	public function getError() :string {

	}

	private function setError(
		string $error_message,
		int $error_code = 500
	) :void {
		
		// save the error
		$this->error = $error_message;

		// if we asked to log errors
		if($this->logErrors) {
			// use the framework's default logging engine
			Logger::warning(
				$error_message,
				$this
			);
		}

		// if we asked to throw exceptions
		if($this->throwExceptionsOnErrors) {
			// use the framework's exceptions
			Throw new Exception(
				$error_message, 
				$error_code
			);
		}
		
	}

	private function requireApiResult() :void {
		// if we have not queries the API yet
		if(!$this->apiResult) {

			// if we miss the API key
			if(!$this->apiKey) {
				// log or throw exception
				$this->setError(
					'Missing Geocoding API Key'
				);
			}

			// if we do reverse geocoding
			if($this->position) {

				$this->apiQuery= (new Curl)
					->get(
						self::API_BASE_URL, 
						[
							'latlng'	=>implode(',', $this->position),
							'key'		=>$this->apiKey
						]
					);

				$this->parseResponse();

			}
			// if we do forward geocoding
			elseif($this->address) {

				$this->apiQuery = (new Curl)
					->get(
						self::API_BASE_URL, 
						[
							'address'	=>$this->address,
							'key'		=>$this->apiKey
						]
					);
					
				$this->parseResponse();
			
			}
			// if we have no clue what we're doing
			else {
				$this->setError('No location provided');
			}

		}
	}


	private function parseResponse() :void {

		// if an error occured
		if(isset($this->apiQuery->error)) {
			$this->setError(
				$this->apiQuery->error
			);
		}
		else {

			// position
			$this->latitude 	= (float) $this->apiQuery
				->results[0]
				->geometry
				->location
				->lat;

			$this->longitude 	= (float) $this->apiQuery
				->results[0]
				->geometry
				->location
				->lng;
			
			$this->position 	= [
				$this->latitude,
				$this->longitude
			];

			// address
			$this->address		= (string) $this->apiQuery
				->results[0]
				->formatted_address;

			// for each address component
			foreach(
				$this->apiQuery->results[0]->address_components as 
				$address_component
			) {

				$section =& $address_component->types;
				$name =& $address_component->long_name;

				if(in_array('postal_code', $section)) {
					$this->zipCode = (string) $name;
				}

				if(in_array('locality', $section)) {
					$this->city = (string) $name;
				}

				if(in_array('country', $section)) {
					$this->country = (string) $name;
				}

				if(in_array('administrative_area_level_2', $section)) {
					$this->department = (string) $name;
				}

				if(in_array('administrative_area_level_1', $section)) {
					$this->region = (string) $name;
				}

				if(in_array('route', $section)) {
					$this->street = (string) $name;
				}

				if(in_array('street_number', $section)) {
					$this->streetNumber = (string) $name;
				}

			}
		}
		

	}


}

?>
