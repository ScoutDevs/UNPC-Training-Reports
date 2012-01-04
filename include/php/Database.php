<?php

class Database {

	const HOST = 'localhost';
	const NAME = 'unpc';
	const USER = 'ben';
	const PASS = 'lk3)2j';

	protected static $mysqli = null;

	protected static function connect() {
		if ( self::$mysqli == null ) {
			self::$mysqli = new MySQLi(self::HOST, self::USER, self::PASS, self::NAME);

			if ( self::$mysqli->connect_error ) {
				throw new Exception('Error connecting to database: '. self::$mysqli->connect_error);
			}
		}
	}

	public static function execute ( $sql ) {
		self::connect();
		$result = self::$mysqli->query($sql, MYSQLI_STORE_RESULT);
		return $result;
	}

	public static function clean ( $var ) {
		self::connect();
		if ( is_scalar($var) ) {
			return self::$mysqli->real_escape_string(trim($var));
		} elseif ( is_array($var) ) {
			foreach ( $var as &$item ) {
				$item = self::clean($item);
			}
			return $var;
		} else {
			throw new Exception('Unable to clean variable of type '.gettype($var));
		}
	}

	public static function getArray ( $sql ) {
		$results = self::execute($sql);
		if ( !$results instanceof MySQLi_Result ) throw new Exception('Database error: '.self::$mysqli->error);
		$data = array();
		while ( $record = $results->fetch_assoc() ) {
			$data[] = $record;
		}
		return $data;
	}
}
