<?php

/*
Plugin Name: WpRecord
Description: Test WpRecord in Wordpress. Not actually a plugin but a library!
Plugin URI: http://github.com/limikael/wprecord
Version: 0.0.1
*/


define("WP_DEBUG",TRUE);

require_once __DIR__."/WpRecord.php";

//print_r(WpRecord::flattenArray(array(array(1),2)));

class RecordTest extends WpRecord {
	public static function initialize() {
		self::field("id","integer not null auto_increment");
		self::field("sometext","varchar(255) not null");
		self::field("otherint","varchar(255) not null");
	}
}

function activate_wpar_test() {
	RecordTest::install();

	$r=new RecordTest();
	$r->sometext="hello";
	$r->save();

	$r->otherint=123;
	$r->save();

	$id=$r->id;

	$a=RecordTest::findOne($id);

	error_log("t: ".$a->sometext);

	$a=RecordTest::findOneByQuery("SELECT * FROM %t WHERE otherint=%s AND sometext=%s",123,"hello");
	error_log("found: ".$a->sometext);

	$a=RecordTest::findOneBy(array(
		"otherint"=>123,
		"sometext"=>"hello"
	));

	$a=RecordTest::findOne($id);
	error_log("before: ".$a->sometext);

	$r->delete();

	//$a=RecordTest::findOne(124123432);
	$a=RecordTest::findOne($id);
	error_log("after: ".$a->sometext);
}

function deactivate_wpar_test() {
	RecordTest::uninstall();
}

register_activation_hook(__FILE__,"activate_wpar_test");
register_deactivation_hook(__FILE__,"deactivate_wpar_test");