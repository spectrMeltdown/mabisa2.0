/* 
NOTES:
ON CASCADE operations are fine for small to medium sized applications
*/

create table permissions (
  id int AUTO_INCREMENT PRIMARY KEY,
  
  users_create BOOLEAN DEFAULT false,
  users_delete BOOLEAN DEFAULT false,
  users_update BOOLEAN DEFAULT false,
  users_read BOOLEAN DEFAULT false,
  
  roles_create BOOLEAN DEFAULT false,
  roles_delete BOOLEAN DEFAULT false,
  roles_update BOOLEAN DEFAULT false,
  roles_read BOOLEAN DEFAULT false,

	criteria_create BOOLEAN DEFAULT false,
  criteria_read BOOLEAN DEFAULT false,
  criteria_update BOOLEAN DEFAULT false,
  criteria_delete BOOLEAN DEFAULT false,
    
	assessment_submissions_create BOOLEAN DEFAULT false,
  assessment_submissions_read BOOLEAN DEFAULT false,
  assessment_submissions_update BOOLEAN DEFAULT false,
  assessment_submissions_delete BOOLEAN DEFAULT false,
  assessment_submissions_approve_disapprove BOOLEAN DEFAULT false,

	assessment_comments_create BOOLEAN DEFAULT false,
  assessment_comments_read BOOLEAN DEFAULT false,
  assessment_comments_update BOOLEAN DEFAULT false,
  assessment_comments_delete BOOLEAN DEFAULT false,

	map_read BOOLEAN DEFAULT false,

	reports_read BOOLEAN DEFAULT false,
  reports_generate BOOLEAN DEFAULT false,
  
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE roles (
    id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(100) NOT NULL UNIQUE,
    allow_barangay boolean NOT NULL,
    permissions_id int NOT NULL,
    last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (permissions_id) REFERENCES permissions(id)
);


CREATE TABLE users (
	id int AUTO_INCREMENT PRIMARY KEY, -- no need to specify not null on primary keys because they already non nullable in nature
  full_name varchar(100) NOT NULL,
  password varchar(100) NOT NULL,
  email varchar(100) NOT NULL UNIQUE,
  mobile_num varchar(10) NOT NULL UNIQUE,
  role_id int,
  is_disabled boolean NOT NULL DEFAULT false,
  profile_pic varchar(200) ,
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL -- don't set to role name, because they can change
);




CREATE TABLE user_roles_barangay (
	user_id int NOT NULL,
	barangay_id varchar(10) NOT NULL, -- just following what was already set in the database
  criteria_id bigint(20) UNSIGNED NOT NULL,-- just following what was already set in the database
  version_id bigint(20) NOT NULL,-- just following what was already set in the database
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, barangay_id, criteria_id, version_id), -- there must not exists a row with the 4 being the same
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
  FOREIGN KEY (barangay_id) REFERENCES refbarangay(brgyid),
	FOREIGN KEY (criteria_id) REFERENCES maintenance_criteria_setup(keyctr),
  FOREIGN KEY (version_id) REFERENCES maintenance_criteria_setup(version_keyctr)
);



CREATE TABLE user_roles (
    user_id int PRIMARY KEY,  -- Each user has a unique role
    permissions_id INT NOT NULL,
    user_roles_barangay_id INT DEFAULT NULL,  -- Nullable for users who don't have a barangay role
    last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,  -- Cascade delete for user
    FOREIGN KEY (permissions_id) REFERENCES permissions(id),
    FOREIGN KEY (user_roles_barangay_id) REFERENCES user_roles_barangay(user_id)  -- Nullable foreign key for barangay role
);