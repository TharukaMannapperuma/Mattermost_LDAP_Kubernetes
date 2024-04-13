-- Create the database user
CREATE USER <mamo_ldap_db_user> WITH ENCRYPTED PASSWORD '<mamo_ldap_db_user_pass>';

GRANT ALL PRIVILEGES ON DATABASE <mamo_ldap_db> TO <mamo_ldap_db_user>;

ALTER DATABASE <mamo_ldap_db> OWNER TO <mamo_ldap_db_user>;