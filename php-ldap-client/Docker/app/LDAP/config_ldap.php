<?php
// LDAP parameters
$ldap_host = getenv('LDAP_HOST') ?: "ldaps://ldap.google.com/";
$ldap_port = intval(getenv('LDAP_PORT')) ?: 636;
$ldap_version = intval(getenv('LDAP_VERSION')) ?: 3;
$ldap_start_tls = boolval(getenv('LDAP_START_TLS')) ?: false;

// Attribute use to identify user on LDAP - ex : uid, mail, sAMAccountName
$ldap_search_attribute = getenv('LDAP_SEARCH_ATTRIBUTE') ?: "uid";

// variable use in resource.php
$ldap_base_dn = getenv('LDAP_BASE_DN') ?: "ou=People,o=Company";
$ldap_filter = getenv('LDAP_FILTER') ?: "(objectClass=*)";

// ldap service user to allow search in ldap
$ldap_bind_dn = getenv('LDAP_BIND_DN') ?: "";
$ldap_bind_pass = getenv('LDAP_BIND_PASSWORD') ?: "";

// Certificates
$ldap_cert_path = getenv('LDAP_CERT_PATH') ?: "/var/www/html/oauth/certs/certificate.crt";
$ldap_key_path = getenv('LDAP_KEY_PATH') ?: "/var/www/html/oauth/certs/key.key";

// Secure LDAP flag
$ldap_secure = boolval(getenv('LDAP_SECURE')) ?: false;
