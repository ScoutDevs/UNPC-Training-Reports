<?php
require_once 'Database.php';

// TODO: create district based on subdistrict code

class DataImport {

	/**
	 * Imports leader-unit file.
	**/
	public static function importLeaderUnitFile ( $filename ) {
		$fh = fopen($filename, 'r');
		$header1 = fgetcsv($fh);
		//var_dump($header1);

		// Remove all leader-unit relationships, since this is a complete import
		self::truncateLeaderUnit();

		// import the data
		$j = 0;
		while ( $data = fgetcsv($fh) ) {
			self::addLeader($data[0], $data[13], $data[14], $data[15], $data[16], $data[17]);
			self::addUnit($data[6], $data[7], $data[10], $data[11], $data[4]);
			self::addSubdistrict($data[4], $data[5]);
			self::addLeaderUnit($data[0], $data[6], $data[7]);

			// progress output
			if ( $j++ % 100 == 0 ) {
				echo $j."\n";
			}
			//var_dump($data);
		}
		fclose($fh);
	}

	/**
	 * Imports the leader-training file
	**/
	public static function importLeaderTrainingFile ( $filename ) {
		$fh = fopen($filename, 'r');
		$header1 = fgetcsv($fh, 100000, "\t");
		$header2 = fgetcsv($fh, 100000, "\t");
		$header3 = fgetcsv($fh, 100000, "\t");

		// add the trainings, which are in the header
		for ( $i = 31; $i < count($header1); $i++ ) {
			self::addTraining($header1[$i], $header2[$i], (strlen(trim($header3[$i])) == 0));
		}

		// loop through each row
		$j = 0;
		while ( $data = fgetcsv($fh, 100000, "\t") ) {
			// add the leader info
			self::addLeader($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[7], $data[9], $data[17], $data[22], $data[23]);
			// if a date is present, record the training
			for ( $i = 31; $i < count($data); $i++ ) {
				if ( strtotime($data[$i]) > 0 ) {
					self::addLeaderTraining($data[0], $header1[$i], date('Y-m-d', strtotime($data[$i])));
				}
			}

			// progress output
			if ( $j++ % 100 == 0 ) {
				echo $j."\n";
			}
		}
		fclose($fh);
	}

	protected static function addTraining ( $code, $name, $active ) {
		$sql = '
			REPLACE INTO `Trainings` (
				`code`,
				`name`,
				`active`
			) VALUES (
				\''.Database::clean($code).'\',
				\''.Database::clean($name).'\',
				\''.Database::clean($active).'\'
			)
		';
		Database::execute($sql);
	}

	protected static function addLeaderTraining ( $leader_id, $training_code, $date ) {
		if ( intval($leader_id) == 0 || strlen(trim($training_code)) == 0 ) return;
		$sql = '
			INSERT IGNORE INTO `Leader_Training` (
				`leader_id`,
				`training_code`,
				`date`
			) VALUES (
				\''.Database::clean($leader_id).'\',
				\''.Database::clean($training_code).'\',
				\''.Database::clean($date).'\'
			)
		';
		Database::execute($sql);
	}

	protected static function addLeader ( $id, $prefix, $first_name, $middle_name, $last_name, $suffix, $email=null, $phone=null, $city=null, $age=null, $sex=null ) {
		if ( intval($id) == 0 ) return;
		$sql = '
			REPLACE INTO `Leaders` (
				`id`,
				`prefix`,
				`first_name`,
				`middle_name`,
				`last_name`,
				`suffix`,
				`email`,
				`phone`,
				`city`,
				`age`,
				`sex`
			) VALUES (
				\''.Database::clean($id).'\',
				\''.Database::clean($prefix).'\',
				\''.Database::clean($first_name).'\',
				\''.Database::clean($middle_name).'\',
				\''.Database::clean($last_name).'\',
				\''.Database::clean($suffix).'\',
				\''.Database::clean($email).'\',
				\''.Database::clean($phone).'\',
				\''.Database::clean($city).'\',
				\''.Database::clean($age).'\',
				\''.Database::clean($sex).'\'
			)
		';
		Database::execute($sql);
	}

	protected static function addUnit ( $unit_type, $unit_number, $program_name, $sponsoring_organization, $subdistrict_code ) {
		if ( intval($unit_number) == 0 ) return;
		$sql = '
			INSERT IGNORE INTO `Units` (
				`unit_number`,
				`unit_type`,
				`program_name`,
				`sponsoring_organization`,
				`subdistrict_code`
			) VALUES (
				\''.Database::clean($unit_number).'\',
				\''.Database::clean($unit_type).'\',
				\''.Database::clean($program_name).'\',
				\''.Database::clean($sponsoring_organization).'\',
				\''.Database::clean($subdistrict_code).'\'
			)
		';
		Database::execute($sql);
	}

	protected static function addSubdistrict ( $code, $name ) {
		if ( strlen(trim($code)) == 0 ) return;
		$district_code = substr($code, strpos($code, '-')-1);
		$sql = '
			INSERT IGNORE INTO `Subdistricts` (
				`code`,
				`district_code`,
				`name`
			) VALUES (
				\''.Database::clean($code).'\',
				\''.Database::clean($district_code).'\',
				\''.Database::clean($name).'\'
			)
		';
		Database::execute($sql);
	}

	protected static function truncateLeaderUnit() {
		$sql = 'TRUNCATE TABLE `Leader_Unit`';
		Database::execute($sql);
	}

	protected static function addLeaderUnit ( $leader_id, $unit_type, $unit_number ) {
		if ( intval($leader_id) == 0 || intval($unit_number) == 0 ) return;
		$sql = '
			INSERT IGNORE INTO `Leader_Unit` (
				`leader_id`,
				`unit_number`,
				`unit_type`
			) VALUES (
				\''.Database::clean($leader_id).'\',
				\''.Database::clean($unit_number).'\',
				\''.Database::clean($unit_type).'\'
			)
		';
		Database::execute($sql);
	}
}
