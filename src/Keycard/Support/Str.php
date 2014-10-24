<?php

namespace Keycard\Support;

class Str extends \Illuminate\Support\Str
{

	protected $_crypt_cipher;
	protected $_crypt_mode;
	protected $_crypt_key;

	public function __construct( $app )
	{
		$this->_crypt_cipher = MCRYPT_DES;
		$this->_crypt_mode = MCRYPT_MODE_NOFB;
		$this->_crypt_key = $app[ 'config' ][ 'app.key' ];

		$size = mcrypt_get_key_size( $this->_crypt_cipher, $this->_crypt_mode );

		if ( isset( $this->_crypt_key[ $size ] ) )
		{
			$this->_crypt_key = static::crc32( $this->_crypt_key );

			if ( isset( $this->_crypt_key[ $size ] ) )
			{
				$this->_crypt_key = substr( $this->_crypt_key, -$size );
			}
		}
	}

	public function encrypt( $value )
	{
		if ( defined( 'MCRYPT_DEV_URANDOM' ) )
		{
			$randomizer = MCRYPT_DEV_URANDOM;
		}
		elseif ( defined( 'MCRYPT_DEV_RANDOM' ) )
		{
			$randomizer = MCRYPT_DEV_RANDOM;
		}
		else
		{
			$randomizer = MCRYPT_RAND;
		}

		$iv = mcrypt_create_iv( mcrypt_get_iv_size( $this->_crypt_cipher, $this->_crypt_mode ), $randomizer );

		$data = mcrypt_encrypt( $this->_crypt_cipher, $this->_crypt_key, $value, $this->_crypt_mode, $iv );

		$hash = static::crc32( $iv . $data . $this->_crypt_key );

		return implode( '.', array_map( 'static::base64UrlEncode', array( $hash, $iv, $data ) ) );
	}

	public function decrypt( $value )
	{
		$value = explode( '.', $value, 3 );

		if ( $value && isset( $value[ 0 ] ) && isset( $value[ 1 ] ) && isset( $value[ 2 ] ) )
		{
			list( $hash, $iv, $data ) = array_map( 'static::base64UrlDecode', $value );

			if ( $hash == static::crc32( $iv . $data . $this->_crypt_key ) )
			{
				return rtrim( mcrypt_decrypt( $this->_crypt_cipher, $this->_crypt_key, $data, $this->_crypt_mode, $iv ), "\0" );
			}
		}

		return false;
	}

	public static function crc32( $value )
	{
		return base_convert( crc32( $value ), 10, 36 );
	}

	public static function base64UrlDecode( $value )
	{
		return base64_decode( strtr( $value, '-_', '+/' ) );
	}

	public static function base64UrlEncode( $value )
	{
		return str_replace( '=', '', strtr( base64_encode( $value ), '+/', '-_' ) );
	}

	public static function unique( $length = 24, $prefix = null )
	{
		static $alphas = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

		list( $uniqid, $entropy ) = explode( '.', uniqid( null, true ) );

		if ( !$prefix )
		{
			$prefix = $alphas[ mt_rand( 0, 25 ) ];
		}

		$uniqid = $prefix . $uniqid . dechex( $entropy );

		for ( $i = strlen( $uniqid ); $i < $length; $i++ )
		{
			$uniqid .= $alphas[ mt_rand( 0, 35 ) ];
		}

		for ( $i = strlen( $prefix ) - 1, $t = strlen( $uniqid ) - 1; $i <= $t; $i++ )
		{
			if ( ctype_alpha( $uniqid[ $i ] ) && mt_rand( 0, 1 ) )
			{
				$uniqid[ $i ] = strtoupper( $uniqid[ $i ] );
			}
		}

		return substr( $uniqid, 0, $length );
	}

}
