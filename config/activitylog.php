<?php

return [

    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => 365,

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => 'activity_log',

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOG_DB_CONNECTION'),

    /*
     * This is the user model used by the application.
     * If set to null the package will try to get it's value from the model
     * configured in auth.providers.users.model.
     */
    'user_model' => \App\Models\User::class,

    /*
     * This is the property of the user model that will be stored in the activity.
     * If set to null the package will try to get the default 'id'.
     */
    'user_id_field' => 'id',

    /*
     * This is the causer_id field stored in the activity.
     * That allows a custom relationship on the table.
     */
    'causer_id_field' => 'causer_id',

    /*
     * This is the causer_type field stored in the activity.
     * That allows a custom relationship on the table.
     */
    'causer_type_field' => 'causer_type',

    /*
     * This is the subject_id field stored in the activity.
     * That allows a custom relationship on the table.
     */
    'subject_id_field' => 'subject_id',

    /*
     * This is the subject_type field stored in the activity.
     * That allows a custom relationship on the table.
     */
    'subject_type_field' => 'subject_type',

    /*
     * Custom properties to be logged with the activity entry.
     * These can be used to log more detailed information to analyze the activity.
     */
    'properties' => [
        // Store IP address
        'ip_address' => true,

        // Store user agent
        'user_agent' => true,

        // Store tenant_id
        'tenant_id' => true,

        // Store request method
        'request_method' => true,

        // Store request url
        'request_url' => true,
    ],

    /*
     * When set to true, the method name will be excluded from activity log.
     */
    'activity_log_ignore_class_methods' => [],
];
