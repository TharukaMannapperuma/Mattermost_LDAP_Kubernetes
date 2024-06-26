-- Create the tables
CREATE TABLE oauth_clients (
    client_id VARCHAR(80) NOT NULL,
    client_secret VARCHAR(80),
    redirect_uri VARCHAR(2000) NOT NULL,
    grant_types VARCHAR(80),
    scope VARCHAR(100),
    user_id VARCHAR(80),
    CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id)
);

CREATE TABLE oauth_access_tokens (
    access_token VARCHAR(40) NOT NULL,
    client_id VARCHAR(80) NOT NULL,
    user_id VARCHAR(255),
    expires TIMESTAMP NOT NULL,
    scope VARCHAR(2000),
    CONSTRAINT access_token_pk PRIMARY KEY (access_token)
);

CREATE TABLE oauth_authorization_codes (
    authorization_code VARCHAR(40) NOT NULL,
    client_id VARCHAR(80) NOT NULL,
    user_id VARCHAR(255),
    redirect_uri VARCHAR(2000),
    expires TIMESTAMP NOT NULL,
    scope VARCHAR(2000),
    CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code)
);

CREATE TABLE oauth_refresh_tokens (
    refresh_token VARCHAR(40) NOT NULL,
    client_id VARCHAR(80) NOT NULL,
    user_id VARCHAR(255),
    expires TIMESTAMP NOT NULL,
    scope VARCHAR(2000),
    CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token)
);

CREATE TABLE users (
    id SERIAL NOT NULL,
    username VARCHAR(255) NOT NULL,
    CONSTRAINT id_pk PRIMARY KEY (id)
);

CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);

-- Insert the default scopes
INSERT INTO
    oauth_clients (
        client_id,
        client_secret,
        redirect_uri,
        grant_types,
        scope,
        user_id
    )
VALUES
    (
        '<client_id>',
        '<client_secret>',
        '<redirect_url>',
        '<grant_types>',
        '<scope>',
        '<user_id>'
    );