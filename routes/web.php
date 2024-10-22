<?php

use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/send-test-email', function () {

    Mail::to('edoaurahman@gmail.com')->send(new OtpEmail());

    return 'Test email sent!';

});