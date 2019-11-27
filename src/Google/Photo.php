<?php
/**
 * PHP Version 5
 * Google Streetview image helper
 * @package Polyfony
 * @link https://github.com/SIB-FRANCE/Polyfony
 * @license http://www.gnu.org/licenses/lgpl.txt GNU General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Google;

use \Polyfony\Config as Config;

class Photo {
	
	// api url
	private static $_api_url = 'https://maps.googleapis.com/maps/api/streetview';

	// options
	private $url;
	private $options;

	// constructor
	public function __construct(
		int $size 	= 600, 
		int $fov 	= 90, 
		int $pitch 	= 10
	) {
		$this->url = self::$_api_url;
		$this->options = [
			'size'	=>$size . 'x' . $size,
			'fov'	=>$fov,
			'pitch'	=>$pitch,
			'key'	=>Config::get('google', 'api_key')
		];
	}

	// set an option
	public function option($key, $value=null) :self {
		// if we are given an array of option
		if(is_array($key)) {
			// for each option
			foreach($key as $index => $value) {
				// set it individually
				$this->option($index, $value);
			}
		}
		else {
			// assign
			$this->options[$key] = $value;
		}
		// return self for chaining
		return $this;
	}

	// set the desired size
	public function size($width, $height) :self {
		// assign
		$this->options['size'] = $width . 'x' . $height;
		// return self for chaining
		return $this;
	}

	// set the position
	public function position($latitude, $longitude) :self {
		// assign
		$this->options['location'] = $latitude . ',' . $longitude;
		// return self for chaining
		return $this;
	}

	// set the street view photo given the address
	public function address(
		string $address, 
		$zip_code = null ,
		$city = null
	) :self {
		// assemble the address portions if any
		$address = trim($address . ' ' . $zip_code . ' ' .$city);
		// if a position is found
		$this->options['location'] = $address;
		// return self for chaining
		return $this;
	}

	// get the url of the photo
	public function url() :string {
		// prepare the url
		$url = $this->url . '?';
		// for each option
		foreach($this->options as $key => $value) {
			// append it
			$url .= urlencode($key) . '=' . urlencode($value) . '&';
		}
		// return the url
		return trim($url,'&');
	}

	// magic conversion
	public function __toString() {
		// return the generated url
		return $this->url();
	}

}

?>
