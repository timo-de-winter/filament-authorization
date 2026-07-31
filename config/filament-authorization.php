<?php

return [
    'guard' => [
        'modifiable' => false,
        'default' => 'web',
    ],

    'roles_permission' => [
        /*
         * Whether this package registers the `roles` permission group itself.
         * Turn it off when the host application registers it — otherwise the
         * group is registered twice, once under the host's tab and once under
         * the default tab.
         */
        'auto_register' => true,
    ],
];
