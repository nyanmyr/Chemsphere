<?php

return [

    'auth' => [
        'invalid_credentials' => 'Invalid email or password.',
        'pending' => 'Your account is pending approval.',
        'login_required' => 'Please log in to access that page.',
        'account_not_found' => 'Account not found.',
        'google_failed' => 'Google authentication failed. Please try again.',
        'google_no_account' => 'No account found for that Google email. Please register first.',
        'google_email_mismatch' => "You signed in to Google as ':google', but your account is registered as ':account'.",
        'google_already_linked' => 'This account is already linked to a different Google account.',
    ],

    'http' => [
        'forbidden' => "You don't have permission to do that.",
        'record_missing' => 'That record no longer exists. It may have been deleted by someone else.',
        'session_expired' => 'Your session expired. Please try again.',
        'login_required' => 'You must be logged in to accessed that page.',
    ],

    'chemical' => [
        'created' => 'Chemical added.',
        'updated' => 'Chemical updated.',
        'deleted' => 'Chemical deleted.',
        'used' => 'Chemical usage recorded.',
        'out_of_stock' => 'This chemical is out of stock.',
        'none_found' => 'No chemicals found matching your search.',
    ],

    'equipment' => [
        'created' => 'Equipment added.',
        'updated' => 'Equipment updated.',
        'deleted' => 'Equipment deleted.',
        'used' => 'Equipment usage recorded.',
        'out_of_stock' => 'No units of this equipment are available.',
        'unavailable' => 'This equipment is currently unavailable.',
        'broken' => 'This equipment is currently broken.',
        'under_maintenance' => 'This equipment is currently under maintenance.',
        'none_found' => 'No equipment found matching your search.',
    ],

    'location' => [
        'created' => 'Location added.',
        'updated' => 'Location updated.',
        'deleted' => 'Location deleted.',
        'none_found' => 'No locations found matching your search.',
    ],

    'user' => [
        'role_updated' => 'User role updated.',
        'cannot_edit_self' => 'You cannot change your own role.',
        'none_found' => 'No users found matching your search.',
    ],

    'alert' => [
        'none_found' => 'You have no alerts.',
    ],

    'audit_log' => [
        'none_found' => 'No audit log entries found matching your search.',
    ],

    'usage_log' => [
        'none_found' => 'No usage log entries found matching your search.',
    ],

];
