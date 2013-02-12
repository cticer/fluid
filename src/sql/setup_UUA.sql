USE crm;

DROP TABLE IF EXISTS users, authorizations;

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

CREATE TABLE authorizations
(
row_id bigint unsigned not null AUTO_INCREMENT,
user_id bigint unsigned not null,/* row_id from the users table */
email varchar(78) not null,/* email address that must be used to be authorized */
authorized_key varchar(40) not null,/* randomly generated authorized key or a key that is a hash of a password entered by user (if authorization_type = PASSWORD) */
salt varchar(3) not null,
authorization_given timestamp not null,/* timestamp for the date when authorization was given */
authorization_exp timestamp not null,/* default to a one hour authorization */
authorization_type varchar(10) not null,/* type of authorization... currently limited to email or password */
private tinyint unsigned not null default 0,/* default to 0... non private */
PRIMARY KEY(row_id)
);
