<?php
	global $DB_QUERY;
	
	// Remarques :
	// search_xxx :
	// SELECT * FROM `lc_news` WHERE `news_content` LIKE '%au%' LIMIT 0 , 30 
	// = nul et lent
    
	//  SELECT * FROM articles  WHERE MATCH(title, body) AGAINST ('PHP') 
	// = rapid necessite MyIsam et indexation FULLTEXT des colonnes "cherchables" avec :
	// // ALTER TABLE lc_file ADD FULLTEXT(file_title,file_name,file_description,file_comment)
	
	// USER
	$table_user = "btl_users";
	$table_role = "btl_roles";
	$DB_QUERY["create_user"] = "INSERT INTO ".$table_user." (role_id, mail, login, hashed_password, last_ip, application, confirmation_token) VALUES (?, ?, ?, ?, ?, ?, ?)";
	$DB_QUERY["exists_user_from_login"] = "SELECT 1 FROM ".$table_user." WHERE login = ? AND application = ?";
	$DB_QUERY["exists_user_from_mail"] = "SELECT 1 FROM ".$table_user." WHERE mail = ? AND application = ?";
	$DB_QUERY["read_user"] = "SELECT * FROM ".$table_user." WHERE user_id = ?";
	$DB_QUERY["read_user_from_login"] = "SELECT * FROM ".$table_user." WHERE login = ? AND application = ?";
	$DB_QUERY["read_user_from_mail"] = "SELECT * FROM ".$table_user." WHERE mail = ? AND application = ?";
	$DB_QUERY["read_user_from_confirmation_token"] = "SELECT * FROM ".$table_user." WHERE confirmation_token = ? AND application = ?";
	$DB_QUERY["read_users_of_page"] = "SELECT * FROM ".$table_user." WHERE application = ? ORDER BY user_id DESC LIMIT ?,?";
	$DB_QUERY["update_user"] = "UPDATE ".$table_user." SET role_id = ?, mail = ?, login = ?, hashed_password = ?, has_confirmed = ?, date_creation = ?, last_ip = ?, marked_for_deletion = ?, marked_for_deletion_date = ?, confirmation_token = ?, application = ? WHERE user_id = ?";
	$DB_QUERY["update_user_last_connection"] = "UPDATE ".$table_user." SET last_ip = ?, date_last_connection = ? WHERE user_id = ?";
	$DB_QUERY["delete_user"] = "DELETE FROM ".$table_user." WHERE user_id = ?";
	$DB_QUERY["search_user"] = "SELECT * FROM ".$table_user." WHERE MATCH(mail, login) AGAINST(? IN BOOLEAN MODE)";
	$DB_QUERY["validate_user_account"] = "UPDATE ".$table_user." SET has_confirmed = 1 WHERE confirmation_token = ?";

?>