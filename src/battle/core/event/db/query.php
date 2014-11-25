<?php
	global $DB_QUERY;
	$table_event = "btl_events";

	$DB_QUERY["exists_event_from_id"] = 
	"SELECT 1 FROM ".$table_event." WHERE event_id = ?";
	$DB_QUERY["create_event"] = 
	"INSERT INTO ".$table_event." (event_id,event_name,event_type,element_id,user_id,date,old_value,new_value) VALUES (?,?,?,?,?,?,?,?)";
	$DB_QUERY["update_event"] = 
	"UPDATE ".$table_event." SET event_name = ?, event_type = ?, element_id = ?, user_id = ?, date = ?, old_value = ?, new_value = ? WHERE event_id = ?";
	$DB_QUERY["select_event"] = 
	"SELECT * FROM ".$table_event."";
	$DB_QUERY["read_event"] = 
	"SELECT * FROM ".$table_event." WHERE event_id = ?";
	$DB_QUERY["count_all_events"] = 
	"SELECT count(*) FROM ".$table_event." event ";
	$DB_QUERY["read_events"] = 
	"SELECT * FROM btl_events ORDER BY event_id	DESC LIMIT ?,?";
	$DB_QUERY["delete_event"] = "DELETE FROM ".$table_event." WHERE event_id = ?";
?>