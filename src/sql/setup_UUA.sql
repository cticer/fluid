USE crm;

DROP TABLE IF EXISTS users;

CREATE TABLE users
(
row_id bigint unsigned not null AUTO_INCREMENT,
first_name varchar(15) not null,
last_name varchar(15) not null,
email varchar(78) not null,
username varchar(30) not null,
password varchar(40) not null,
salt varchar(3) not null,
verification_code varchar(65) not null,/* code that is sent to their email so they can verify their email */
verified tinyint unsigned not null default 0,/* 1 means account verified */
PRIMARY KEY(row_id)
);	
