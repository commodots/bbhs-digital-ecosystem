<?php
return [
 'notifications'=>['email'=>env('BBHS_NOTIFICATIONS_EMAIL',false)],
 'dues'=>['allow_pending_payment'=>env('BBHS_DUES_ALLOW_PENDING_PAYMENT',true)],
 'donations'=>['bank_name'=>env('BBHS_DONATION_BANK_NAME'),'account_name'=>env('BBHS_DONATION_ACCOUNT_NAME'),'account_number'=>env('BBHS_DONATION_ACCOUNT_NUMBER')],
];
