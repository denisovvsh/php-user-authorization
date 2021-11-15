show databases;
use work5;
show tables;
describe orders;
show processlist;

mysqldump -u root -p --no-data work5 > ./database.sql

После создания БД выполнить
sudo mysql
SELECT user,authentication_string,plugin,host FROM mysql.user;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
FLUSH PRIVILEGES;


CREATE TABLE users (
	id INT NOT NULL AUTO_INCREMENT,
	fio VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	login VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE orders (
	id INT NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	price INT(11) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE session_login (
	id INT NOT NULL AUTO_INCREMENT,
	user_id int NOT NULL,
	hash VARCHAR(100) NOT NULL,
	date DATE NOT NULL,
	PRIMARY KEY (id)
);

DROP TABLE users
