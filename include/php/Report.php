<?php
require_once 'Database.php';

class Report {

	public static function getDistricts() {
		$sql = 'SELECT * FROM `Districts` ORDER BY `name`';
		return Database::getArray($sql);
	}

	public static function getSubdistricts() {
		$sql = 'SELECT * FROM `Subdistricts` ORDER BY `name`';
		return Database::getArray($sql);
	}

	public static function getUnits() {
		$sql = 'SELECT DISTINCT `unit_number`, `sponsoring_organization` FROM `Units` ORDER BY `unit_number`';
		return Database::getArray($sql);
	}

	public static function getUnitTypes() {
		$sql = 'SELECT DISTINCT `unit_type` FROM `Units` ORDER BY `unit_type`';
		return Database::getArray($sql);
	}

	public static function getTrainings ( $active_only=true ) {
		$sql = 'SELECT * FROM `Trainings`';
		if ( $active_only ) {
			$sql .= ' WHERE `active` = 1';
		}
		$sql .= ' ORDER BY `name`';
		return Database::getArray($sql);
	}

	public static function getSubdistrictByDistrict ( $district_code ) {
		$sql = 'SELECT * FROM `Subdistrict` WHERE `district_code` = \''.Database::clean($district_code).'\' ORDER BY `name`';
		return Database::getArray($sql);
	}

	public static function getUnitsBySubdistrict ( $subdistrict_code ) {
		$sql = 'SELECT DISTINCT `unit_type` FROM `Units` WHERE `subdistrict_code` = \''.Database::clean($subdistrict_code).'\' ORDER BY `unit_type`';
		return Database::getArray($sql);
	}

	public static function getLeaderSearch ( $districts, $subdistricts, $units, $unit_types, $trainings, $start_date, $end_date, $summarize_by ) {
		$where = array();
		$sql = '
			SELECT
				`l`.*,
				`u`.`unit_type`,
				`u`.`unit_number`,
				`u`.`sponsoring_organization`,
				`u`.`subdistrict_code`,
				`s`.`district_code`,
				`s`.`name` `subdistrict_name`,
				`d`.`name` `district_name`,
				`t`.`code` `training_code`,
				`t`.`name` `training_name`
			FROM
				`Leaders` `l`
				INNER JOIN `Leader_Unit` `lu` ON
					`l`.`id` = `lu`.`leader_id`
				INNER JOIN `Units` `u` ON
					`u`.`unit_type` = `lu`.`unit_type`
					AND `u`.`unit_number` = `lu`.`unit_number`
				INNER JOIN `Subdistricts` `s` ON
					`s`.`code` = `u`.`subdistrict_code`
				INNER JOIN `Districts` `d` ON
					`d`.`code` = `s`.`district_code`
				INNER JOIN `Leader_Training` `lt` ON
					`lt`.`leader_id` = `l`.`id`
				INNER JOIN `Trainings` `t` ON
					`lt`.`training_code` = `t`.`code`
					AND `t`.`active` = 1
		';

		if ( count($units) ) {
			$where[] .= '`u`.`unit_number` IN ('.implode(',', Database::clean($units)).')';
		} elseif ( count($subdistricts) ) {
			$where[] = '`u`.`subdistrict_code` IN (\''.implode('\',\'', Database::clean($subdistricts)).'\')';
		} elseif( count($districts) ) {
			$where[] = '`s`.`district_code` IN (\''.implode('\',\'', Database::clean($districts)).'\')';
		}

		if ( count($unit_types) ) {
			$where[] = '`u`.`unit_type` IN (\''.implode('\',\'', Database::clean($unit_types)).'\')';
		}

		if ( count($trainings) ) {
			$where[] = '`lt`.`training_code` IN (\''.implode('\',\'', Database::clean($trainings)).'\')';
		}

		$sql .= '
			WHERE
		'.implode("\nAND ", $where).'
			LIMIT 1001
		';
		echo $sql;

		return Database::getArray($sql);
	}
}
