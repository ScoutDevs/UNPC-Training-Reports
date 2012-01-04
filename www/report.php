<?php
require_once '../include/php/Report.php';

$districts = Report::getDistricts();
$subdistricts = Report::getSubdistricts();
$units = Report::getUnits();
$types = Report::getUnitTypes();
$trainings = Report::getTrainings();

function getVar ( $var ) {
	if ( isset($_POST[$var]) ) return $_POST[$var]; else return null;
}

function getArray ( $var ) {
	$arr = getVar($var);
	if ( !is_array($arr) || in_array('All', $arr) ) return array();
	return $arr;
}

$selected_districts = getArray('districts');
$selected_subdistricts = getArray('subdistricts');
$selected_units = getArray('units');
$selected_unit_types = getArray('unit_types');
$selected_trainings = getArray('trainings');
$selected_start_date = getVar('start_date');
$selected_end_date = getVar('end_date');
$selected_summarize_by = getArray('summarize_by');

if ( $_POST['submit'] ) {
	$report_data = Report::getLeaderSearch($selected_districts, $selected_subdistricts, $selected_units, $selected_unit_types, $selected_trainings, $selected_start_date, $selected_end_date, $selected_summarize_by);
}

?>

<h2>Report:</h2>

<form method="POST">
	<input type="hidden" name="submit" value="1">

	<div style="float: left;">
		District:<br>
		<select name="districts[]" size="20" multiple>
			<option value="All" <?php if ( !$selected_districts ) echo ' selected';?>>All</option> <?php
			foreach ( $districts as $item ) {
				if ( in_array($item['code'], $selected_districts) ) $selected = ' selected'; else $selected = '';
				echo '<option value="'.$item['code'].'"'.$selected.'>'.$item['name'].'</option>';
			} ?>
		</select>
	</div>

	<div style="float: left;">
		Subdistrict:<br>
		<select name="subdistricts[]" size="20" multiple>
			<option value="All" <?php if ( !$selected_subdistricts ) echo ' selected';?>>All</option> <?php
			foreach ( $subdistricts as $item ) {
				if ( in_array($item['code'], $selected_subdistricts) ) $selected = ' selected'; else $selected = '';
				echo '<option value="'.$item['code'].'"'.$selected.'>'.$item['name'].'</option>';
			} ?>
		</select>
	</div>

	<div style="float: left;">
		Unit:<br>
		<select name="units[]" size="20" multiple>
			<option value="All" <?php if ( !$selected_units ) echo ' selected';?>>All</option> <?php
			foreach ( $units as $item ) {
				if ( in_array($item['unit_number'], $selected_units) ) $selected = ' selected'; else $selected = '';
				echo '<option value="'.$item['unit_number'].'"'.$selected.'>'.$item['unit_number'].' - '.$item['sponsoring_organization'].'</option>';
			} ?>
		</select>
	</div>

	<div style="float: left;">
		Type:<br>
		<select name="unit_types[]" size="20" multiple>
			<option value="All" <?php if ( !$selected_unit_types ) echo ' selected';?>>All</option> <?php
			foreach ( $types as $item ) {
				if ( in_array($item['unit_type'], $selected_unit_types) ) $selected = ' selected'; else $selected = '';
				echo '<option value="'.$item['unit_type'].'"'.$selected.'>'.$item['unit_type'].'</option>';
			} ?>
		</select>
	</div>

	<div style="float: left;">
		Trainings:<br>
		<select name="trainings[]" size="20" multiple>
			<option value="All" <?php if ( !$selected_trainings ) echo ' selected';?>>All</option> <?php
			foreach ( $trainings as $item ) {
				if ( in_array($item['code'], $selected_trainings) ) $selected = ' selected'; else $selected = '';
				echo '<option value="'.$item['code'].'"'.$selected.'>'.$item['name'].'</option>';
			} ?>
		</select>
	</div>

	<div style="float: left;">
		Training Date:<br>
		<input type="text" name="start_date" value="<?=$selected_start_date?>"> - <input type="text" name="end_date" value="<?=$selected_end_date?>">
	</div>

	<div>
		Summarize by:<br>
		<input type="checkbox" name="summarize_by[]" value="district"<?if ( in_array('district', $selected_summarize_by) ) echo ' checked';?>> District<br>
		<input type="checkbox" name="summarize_by[]" value="subdistrict"<?if ( in_array('subdistrict', $selected_summarize_by) ) echo ' checked';?>> Subdistrict<br>
		<input type="checkbox" name="summarize_by[]" value="unit"<?if ( in_array('unit', $selected_summarize_by) ) echo ' checked';?>> Unit<br>
		<input type="checkbox" name="summarize_by[]" value="unit_type"<?if ( in_array('unit_type', $selected_summarize_by) ) echo ' checked';?>> Type<br>
		<input type="checkbox" name="summarize_by[]" value="training"<?if ( in_array('training', $selected_summarize_by) ) echo ' checked';?>> Training<br>
	</div>

	<input type="submit" value="Run Report">
</form>

<?php
var_dump($report_data);
?>
