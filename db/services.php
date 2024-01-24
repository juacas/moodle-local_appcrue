 <?php

    $services = array(
        'local_appcrue_external' => array( // the name of the web service
            'functions' => array('local_appcrue_send_instant_messages',
                                'local_appcrue_send_instant_message',
                                'local_appcrue_notify_grade'), // web service functions of this service
            'requiredcapability' => '', // if set, the web service user need this capability to access
            // any function of this service. For example: 'some/capability:specified'
            'restrictedusers' => 1, // if enabled, the Moodle administrator must link some user to this service
            // into the administration https://server/admin/webservice/service_users.php?id=7
            'enabled' => 1, // if enabled, the service can be reachable on a default installation
            'shortname' => '', // optional – but needed if restrictedusers is set so as to allow logins.
            'downloadfiles' => 0, // allow file downloads.
            'uploadfiles' => 0 // allow file uploads.
        ),
        
    );
$functions = array(
        'local_appcrue_send_instant_messages' => array(         //web service function name
            'classname'   => 'local_appcrue_external',  //class containing the external function OR namespaced class in classes/external/XXXX.php
            'methodname'  => 'send_instant_messages',          //external function name
            'classpath'   => 'local/appcrue/externallib.php',  //file containing the class/external function - not required if using namespaced auto-loading classes.
                                                    // defaults to the service's externalib.php
            'description' => 'Sends a list of instant messages to users identified by any user field.',    //human readable description of the web service function
            'type'        => 'write',                  //database rights of the web service function (read, write)
            'ajax' => true,        // is the service available to 'internal' ajax calls.
            'capabilities' => 'moodle/site:sendmessage', // comma separated list of capabilities used by the function.
        ),
        'local_appcrue_send_instant_message' => array(         //web service function name
            'classname'   => 'local_appcrue_external',  //class containing the external function OR namespaced class in classes/external/XXXX.php
            'methodname'  => 'send_instant_message',          //external function name
            'classpath'   => 'local/appcrue/externallib.php',  //file containing the class/external function - not required if using namespaced auto-loading classes.
            // defaults to the service's externalib.php
            'description' => 'Sends instant message to one user identified by any user field.',    //human readable description of the web service function
            'type'        => 'write',                  //database rights of the web service function (read, write)
            'ajax' => true,        // is the service available to 'internal' ajax calls.
            'capabilities' => 'moodle/site:sendmessage', // comma separated list of capabilities used by the function.
        ),
        'local_appcrue_notify_grade' => array(         //web service function name
            'classname'   => 'local_appcrue_external',  //class containing the external function OR namespaced class in classes/external/XXXX.php
            'methodname'  => 'notify_grade',          //external function name
            'classpath'   => 'local/appcrue/externallib.php',  //file containing the class/external function - not required if using namespaced auto-loading classes.
            // defaults to the service's externalib.php
            'description' => 'Notify a oficial grade with revision info and sends instant message to one user identified by any user field.',    //human readable description of the web service function
            'type'        => 'write',                  //database rights of the web service function (read, write)
            'ajax' => true,        // is the service available to 'internal' ajax calls.
            'capabilities' => 'moodle/site:sendmessage', // comma separated list of capabilities used by the function.
        ),
);