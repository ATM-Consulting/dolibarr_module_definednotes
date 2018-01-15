CREATE TABLE llx_c_predefinednotes ( 
	rowid int(11) NOT NULL AUTO_INCREMENT
	, entity int(11) NOT NULL DEFAULT 1
	, module varchar(32) DEFAULT NULL
	, datec datetime DEFAULT NULL
	, tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	, label varchar(255) DEFAULT NULL
	, active integer NOT NULL DEFAULT 1
	, content mediumtext
	, PRIMARY KEY (rowid)
	, UNIQUE KEY uk_c_email_templates (entity,label)
);