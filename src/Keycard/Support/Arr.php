<?php

namespace Keycard\Support;

class Arr extends \Illuminate\Support\Arr
{

	public static function isAssoc( array $array )
	{
		$keys = array_keys( $array );

		return array_keys( $keys ) !== $keys;
	}

	public static function merge( $array1, $array2 )
	{
		if ( static::isAssoc( $array2 ) )
		{
			foreach ( $array2 AS $key => $value )
			{
				if ( is_array( $value ) && isset( $array1[ $key ] ) && is_array( $array1[ $key ] ) )
				{
					$array1[ $key ] = static::merge( $array1[ $key ], $value );
				}
				else
				{
					$array1[ $key ] = $value;
				}
			}
		}
		else
		{
			foreach ( $array2 AS $value )
			{
				if ( !in_array( $value, $array1, true ) )
				{
					$array1[] = $value;
				}
			}
		}

		if ( func_num_args() > 2 )
		{
			foreach ( array_slice( func_get_args(), 2 ) AS $array2 )
			{
				if ( static::is_assoc( $array2 ) )
				{
					foreach ( $array2 AS $key => $value )
					{
						if ( is_array( $value ) && isset( $array1[ $key ] ) && is_array( $array1[ $key ] ) )
						{
							$array1[ $key ] = static::merge( $array1[ $key ], $value );
						}
						else
						{
							$array1[ $key ] = $value;
						}
					}
				}
				else
				{
					foreach ( $array2 AS $value )
					{
						if ( !in_array( $value, $array1, true ) )
						{
							$array1[] = $value;
						}
					}
				}
			}
		}

		return $array1;
	}

	public static function jsonEncode( $value )
	{
		if ( is_array( $value ) )
		{
			$value = json_encode( $value );

			if ( $value )
			{
				return $value;
			}
		}

		return '[]';
	}

	public static function jsonDecode( $value )
	{
		$value = json_decode( $value, true );

		if ( $value )
		{
			if ( is_array( $value ) )
			{
				return $value;
			}
		}

		return array();
	}

}
