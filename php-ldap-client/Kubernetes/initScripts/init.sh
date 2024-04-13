#!/bin/sh

# Install the required packages
apk --update add postgresql-client
rm -rf /var/cache/apk/*

#Functions
info() { echo -e '\e[32m'$1'\e[m'; }
warn() { echo -e '\e[33m'$1'\e[m'; }
error() { echo -e '\e[31m'$1'\e[m'; }

# OAuth configuration
client_id=${CLIENT_ID:-"123456789"} && info "Client ID: $client_id"
client_secret=${CLIENT_SECRET:-"987654321"} && info "Client Secret: $client_secret"
redirect_url=${REDIRECT_URL:-"http://localhost/signup/gitlab/complete"} && info "Redirect URL: $redirect_url"
grant_types=${GRANT_TYPES:-"authorization_code"} && info "Grant Types: $grant_types"
scope=${SCOPE:-"api"} && info "Scope: $scope"
user_id=${USER_ID:-""} && info "User ID: $user_id"

# Database configuration
db_user=${DB_USER:-"oauth"} && info "Database User: $db_user"
db_name=${DB_NAME:-"oauth_db"} && info "Database Name: $db_name"
db_pass=${DB_PASS:-"oauth_secure-pass"}
db_host=${POSTGRES_HOST:-"localhost"} && info "Database Host: $db_host"
db_port=${POSTGRES_PORT:-"5432"} && info "Database Port: $db_port"
db_root_user=${POSTGRES_USER:-"postgres"} && info "Database Root User: $db_root_user"
db_root_pass=${POSTGRES_PASSWORD:-"postgres"}
db_user_sql=${INPUT_DB_FILE:-"./init_user.sql"} && info "SQL File Path: $db_user_sql"
db_schema_sql=${INPUT_SCHEMA_FILE:-"./init_schema.sql"} && info "Schema File Path: $db_schema_sql"
db_user_sql_out=${OUTPUT_DB_FILE:-"./init_user_mod.sql"} && info "Output File Path: $db_user_sql_out"
db_schema_sql_out=${OUTPUT_SCHEMA_FILE:-"./init_schema_mod.sql"} && info "Output Schema File Path: $db_schema_sql_out"

# Modify SQL statement to use the correct details

sed -e "s|<mamo_ldap_db>|${db_name}|g" \
    -e "s|<mamo_ldap_db_user>|${db_user}|g" \
    -e "s|<mamo_ldap_db_user_pass>|${db_pass}|g" \
    "$db_user_sql" >"$db_user_sql_out"

sed -e "s|<client_id>|${client_id}|g" \
    -e "s|<client_secret>|${client_secret}|g" \
    -e "s|<redirect_url>|${redirect_url}|g" \
    -e "s|<grant_types>|${grant_types}|g" \
    -e "s|<scope>|${scope}|g" \
    -e "s|<user_id>|${user_id}|g" \
    "$db_schema_sql" >"$db_schema_sql_out"

info "SQL file has been updated with the correct details"
export PGPASSWORD=$db_root_pass

# Check if the database exists
info "Checking if the database $db_name exists"
db_exists=$(psql -h $db_host -p $db_port -U $db_root_user -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname='$db_name'")
if [ "$db_exists" = "1" ]; then
    error "Database $db_name already exists"
    exit 0
fi

# Create the database
info "Creating the database $db_name"
psql -h $db_host -p $db_port -U $db_root_user -d postgres -c "CREATE DATABASE $db_name;"

# Create the user
info "Creating the user $db_user"
psql -h $db_host -p $db_port -U $db_root_user -d $db_name -f $db_user_sql_out

export PGPASSWORD=$db_pass
# Initializing the database with schema and user
info "Initializing the database with schema"
psql -h $db_host -p $db_port -U $db_user -d $db_name -f $db_schema_sql_out
