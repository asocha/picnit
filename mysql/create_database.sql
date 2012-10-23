DROP DATABASE IF EXISTS picnit;
CREATE DATABASE picnit;
USE picnit;

CREATE TABLE members (
	member_id	INT UNSIGNED	NOT NULL AUTO_INCREMENT,
	is_admin	BOOL		NOT NULL,
	is_suspended	BOOL		NOT NULL,
	username	VARCHAR(16)	NOT NULL,
	password	BINARY(32)	NOT NULL,
	email		VARCHAR(64)	NOT NULL,
	PRIMARY KEY (member_id)
);

CREATE TABLE images (
	image_id	INT UNSIGNED	NOT NULL AUTO_INCREMENT,
	owner_id	INT UNSIGNED	NOT NULL,
	is_public	BOOL		NOT NULL,
	link		CHAR(48)	NOT NULL,
	date_added	DATE		NOT NULL,
	PRIMARY KEY (image_id),
	FOREIGN KEY (owner_id) REFERENCES members (member_id) ON DELETE CASCADE
);

CREATE TABLE comments (
	comment_id	INT UNSIGNED	NOT NULL AUTO_INCREMENT,
	commenter_id	INT UNSIGNED	NOT NULL,
	image_id	INT UNSIGNED	NOT NULL,
	text		TEXT		NOT NULL,
	PRIMARY KEY (comment_id),
	FOREIGN KEY (commenter_id) REFERENCES members (member_id) ON DELETE CASCADE,
	FOREIGN KEY (image_id) REFERENCES images (image_id) ON DELETE CASCADE
);

CREATE TABLE mem_tags (
	member_id	INT UNSIGNED	NOT NULL,
	image_id	INT UNSIGNED	NOT NULL,
	date_tagged	DATE		NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members (member_id) ON DELETE CASCADE,
	FOREIGN KEY (image_id) REFERENCES images (image_id) ON DELETE CASCADE,
	PRIMARY KEY (member_id, image_id)
);

CREATE TABLE favorites (
	member_id	INT UNSIGNED	NOT NULL,
	image_id	INT UNSIGNED	NOT NULL,
	FOREIGN KEY (member_id) REFERENCES members (member_id) ON DELETE CASCADE,
	FOREIGN KEY (image_id) REFERENCES images (image_id) ON DELETE CASCADE,
	PRIMARY KEY (member_id, image_id)
);

CREATE TABLE categories (
	category_id	INT UNSIGNED	NOT NULL,
	category	TEXT		NOT NULL,
	PRIMARY KEY (category_id)
);

CREATE TABLE category_tags (
	category_id	INT UNSIGNED	NOT NULL,
	image_id	INT UNSIGNED	NOT NULL,
	FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE CASCADE,
	FOREIGN KEY (image_id) REFERENCES images (image_id) ON DELETE CASCADE,
	PRIMARY KEY (category_id, image_id)
);
