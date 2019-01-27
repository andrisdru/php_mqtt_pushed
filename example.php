<?php

require_once("class_pushed.php");

$parameters = array(
	 /* MQTT client configuratoin*/
	 "broker" => "localhost",
	 "port" => "1883",
	 "user" => "userman",
	 "password" => "mnypasswrdz",
	 /* pushed condiguration */
	 "p_app_key" => "D36UTOz57asfdsfafdaO",
	 "p_app_secret" => "G8pWg4cbNJK3H35csfdsfadft7j0TyLXDbJDqLsILCGst9",
	 "p_target_type" => "channel",
	 "p_target_alias" => "sdfghtr"
	);
	
$topics = array(
	array("topic"=>"spameris/feeds/katls/vent", /* MQTT topic */
	      "condition"=>"==",    /* PHP comparsion operator http://php.net/manual/en/language.operators.comparison.php */
	      "condition_value"=>1, /*message value */
	      "message"=>"Katls: ", /* message text */
	      "once"=>true,      /* send only on condition change */
	      "lastval"=>false), /* dont touch */
	      
	array("topic"=>"spameris/feeds/katls/out_temp",
	      "condition"=>"<",
	      "condition_value"=>45,
	      "message"=>"Temperatura zem: ",
	      "once"=>true,
	      "lastval"=>false
	      ),
	      
	array("topic"=>"spameris/feeds/detector1/alarm",
	      "condition"=>"<>",
	      "condition_value"=>'OK',
	      "message"=>"1 stāva detektors: ",
	      "once"=>true,
	      "lastval"=>false), 
	      
	array("topic"=>"spameris/feeds/detector/alarm",
	      "condition"=>"<>",
	      "condition_value"=>'OK',
	      "message"=>"1 stāva detektors: ",
	      "once"=>true,
	      "lastval"=>false),        
	);


$client = new pushedmq($parameters,$topics);

?> 
