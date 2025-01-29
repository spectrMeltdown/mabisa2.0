# New user flow
add user
add personal info (each unique)
assign role (reset options below when changing role)

(show below only when role is selected, and if the role requires it)
assign barangay(s) (reset options below when changing barangay)

(show below only when barangay(s) is selected, and if the role requires it)
assign criteria (active version must be set!)

# New role flow
add role
add name (unique)
assign barangay? (bool)
assign barangay(s)? (bool)

(show below only when role is selected)
assign barangay(s) (reset options below when changing barangay)

(show below only when barangay(s) is selected)
assign criteria? (bool)
assign criteria
### NOTE!
- active version must be set!

# New Structure
- dynamic roles:
	- set available permissions for a role
		- users
			- add, edit, delete, update
		- criteria
			- approve, comment, delete (with specific barangay)
- dynamic user permissions:
	- select individual user permissions based on the assigned role

ALL AVAILABLE PERMISSIONS
users
- account info
	- create, read, update
roles
- CRUD

criteria
- versions
	- CRUD
- setup
	- update 
- submissions
	- create, delete
	- approve/disapprove
- comments
	- create, delete
reports
- generate
