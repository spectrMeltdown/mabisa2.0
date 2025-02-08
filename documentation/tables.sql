/* 
NOTES:
ON CASCADE operations are fine for small to medium sized applications
*/

create table permissions (
  id int AUTO_INCREMENT PRIMARY KEY,

  super_admin_create BOOLEAN DEFAULT false,
  super_admin_read BOOLEAN DEFAULT false,
  super_admin_delete BOOLEAN DEFAULT false,
  
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
    allow_barangay boolean NOT NULL DEFAULT false,
    permissions_id int,
    last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (permissions_id) REFERENCES permissions(id) ON DELETE SET NULL
);


CREATE TABLE users (
	id int AUTO_INCREMENT PRIMARY KEY, -- no need to specify not null on primary keys because they already non nullable in nature
  full_name varchar(100) NOT NULL,
  username varchar(100) NOT NULL UNIQUE,
  password varchar(100) NOT NULL,
  email varchar(100) NOT NULL UNIQUE,
  mobile_num varchar(10) NOT NULL UNIQUE,
  role_id int,
  is_disabled boolean NOT NULL DEFAULT false,
  profile_pic varchar(200),
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL -- don't set to role name, because they can change
);

-- alter table users
-- add username varchar(100) not null unique
-- after full_name;

CREATE TABLE user_roles_barangay (
	user_id int NOT NULL,
	barangay_id varchar(10), -- just following what was already set in the database
  indicator_id bigint(20),-- just following what was already set in the database
  permission_id int,
  last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE (user_id, barangay_id, indicator_id, permission_id), -- there must not exist a row with the 4 being the same. Using UNIQUE because primary keys cannot be null
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
  FOREIGN KEY (barangay_id) REFERENCES refbarangay(brgyid) ON DELETE SET NULL, 
	FOREIGN KEY (indicator_id) REFERENCES maintenance_area_indicators(keyctr) ON DELETE SET NULL,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE SET NULL
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