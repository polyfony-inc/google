<?php
/**
 * PHP Version 7
 * Cache provider for images provided by Google APIs
 * as to reduce the billing cost of such APIs
 * @package Polyfony
 * @link https://github.com/polyfony-inc/polyfony
 * @license http://www.gnu.org/licenses/lgpl.txt GNU General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Google;

use \Polyfony\Config as Config;
use \Curl\Curl;

class Cache {

	const STORAGE_PATH 	= '../Private/Storage/Cache/Assets/Img/';
	const URL_PATH 		= '/Assets/Img/Cache/';

	// provide a local url proxy
	public static function getProxiedUrl(
		string $remote_url
	) :string {

		self::createCacheFolder();

		// if we already have that url's content cached
		if(Cache::has($remote_url)) {
			return self::generateUrl($remote_url);
		}
		// we do not have it yet
		else {
			// get it
			(new Curl)
				->download(
					$remote_url, 
					self::getLocalPath($remote_url)
				);
			// return the proxied url
			return self::generateUrl($remote_url);
		}

	}

	private static function getLocalPath(
		string $remote_url
	) :string {
		return 
			self::STORAGE_PATH . 
			self::getHash($remote_url);
	}

	private static function getHash(
		string $remote_url
	) :string {
		return hash(
			'sha512',
			json_encode([
				'polyfony-inc/google',
				$remote_url
			])
		);
	}

	private static function generateUrl(
		string $remote_url
	) :string {

		return 
			self::URL_PATH . 
			self::getHash($remote_url);

	}

	private static function has(
		string $remote_url
	) :bool {

		return file_exists(
			self::getLocalPath($remote_url)
		);

	}

	private static function createCacheFolder() :void {

		// create the storage folder
		file_exists(self::STORAGE_PATH) ?: mkdir(
			self::STORAGE_PATH, 
			0777, 
			true
		);
		// create a symlink too
		is_link('./'.trim(self::URL_PATH,'/')) ?: symlink( 
			'../../'.self::STORAGE_PATH, 
			'./'.trim(self::URL_PATH,'/')
		);

	}

}

?>
