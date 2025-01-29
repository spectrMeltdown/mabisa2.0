
CREATE TABLE users (
	id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    full_name varchar(100) NOT NULL,
    password varchar(100) NOT NULL,
    email varchar(100) NOT NULL UNIQUE,
    mobile_num varchar(10) NOT NULL UNIQUE,
    role_name varchar(100) NOT NULL,
    profile_pic varchar(200),
    last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

    Foreign Key (role_name) REFERENCES roles(name), -- this is valid because role names have to be unique anyway
);

CREATE TABLE roles (
    id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name varchar(100) NOT NULL UNIQUE,
    permissions_id int NOT NULL,
    allow_barangay bool NOT NULL,
    last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

    FOREIGN KEY (permissions_id) REFERENCES permissions(id),
);


-- also known as junction table, btw
CREATE TABLE user_roles {
	user_id int NOT NULL,
  permissions_id int NOT NULL,
	barangay_id int DEFAULT NULL,
  criteria_id int NOT NULL,
  criteria_version_id int NOT NULL,
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

	PRIMARY KEY (user_id, barangay_id), -- composite key
	FOREIGN KEY (user_id) REFERENCES users(id),
	FOREIGN KEY (barangay_id) REFERENCES refbarangay(brgyid),
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (permissions_id) REFERENCES permissions(id),
  FOREIGN KEY (barangay_id) REFERENCES refbarangay(brgyid)
};


create table permissions (
  id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
  
  users_create BOOL DEFAULT false,
  users_delete BOOL DEFAULT false,
  users_update BOOL DEFAULT false,
  users_read BOOL DEFAULT false,
  
  roles_create BOOL DEFAULT false,
  roles_delete BOOL DEFAULT false,
  roles_update BOOL DEFAULT false,
  roles_read BOOL DEFAULT false,

	criteria_create BOOL DEFAULT false,
  criteria_read BOOL DEFAULT false,
  criteria_update BOOL DEFAULT false,
  criteria_delete BOOL DEFAULT false,
    
	assessment_submissions_create BOOL DEFAULT false,
  assessment_submissions_read BOOL DEFAULT false,
  assessment_submissions_update BOOL DEFAULT false,
  assessment_submissions_delete BOOL DEFAULT false,
  assessment_submissions_approve_disapprove BOOL DEFAULT false,

	assessment_comments_create BOOL DEFAULT false,
  assessment_comments_read BOOL DEFAULT false,
  assessment_comments_update BOOL DEFAULT false,
  assessment_comments_delete BOOL DEFAULT false,

	map_read BOOL DEFAULT false,

	reports_read BOOL DEFAULT false,
  reports_generate BOOL DEFAULT false
  
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
