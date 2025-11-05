<?php

return [
    'realm_url' => env('KEYCLOAK_REALM_URL', ''),
    'client_id' => env('KEYCLOAK_CLIENT_ID', ''),
    'user_provider_credential' => 'email',
    'append_decoded_token' => true,
    'load_user_from_database' => true,
    'token_principal_attribute' => 'email',
    'create_user_if_not_exists' => true,

    'user_mapping' => [
        'email' => 'email',
    ],

    'roles_claim' => 'realm_access.roles',

    'token_encryption_algorithm' => env('KEYCLOAK_TOKEN_ENCRYPTION_ALGORITHM', 'RS256'),

    'user_provider_custom_retrieve_method' => env('KEYCLOAK_USER_PROVIDER_CUSTOM_RETRIEVE_METHOD', null),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', null),

    'ignore_resources_validation' => env('KEYCLOAK_IGNORE_RESOURCES_VALIDATION', false),

    'leeway' => env('KEYCLOAK_LEEWAY', 0),

    'input_key' => env('KEYCLOAK_TOKEN_INPUT_KEY', null)
];
