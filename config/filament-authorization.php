<?php

return [
    'guard' => [
        'modifiable' => false,
        'default' => 'web',
    ],

    'roles_permission' => [
        // When true, the package auto-registers a "roles" permission group
        // (view/update/create/delete) under the default tab. Disable this if
        // your application registers its own roles permission group.
        'auto_register' => true,
    ],
];
