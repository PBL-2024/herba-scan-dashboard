<?php

namespace App\Services;

use App\Mail\OtpEmail;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    public function generateOTP($email)
    {
        // Buat OTP 6 digit
        $otp = rand(100000, 999999);

        // Simpan atau update OTP di tabel password_reset_tokens
        PasswordReset::updateOrCreate(
            ['email' => $email], // Update jika email sudah ada
            [
                'token' => $otp, // Simpan OTP di kolom 'token'
                'created_at' => Carbon::now(), // Simpan timestamp
            ]
        );

        // Kirim OTP ke email
        $this->sendOTPEmail($email, $otp);

        return $otp;
    }

    public function sendOTPEmail($email, $otp)
    {
        // Fungsi untuk mengirim email, bisa menggunakan Laravel Mail atau notifikasi
        Mail::to($email)->send(new OtpEmail($otp));
    }

    public function verifyOTP($email, $otp)
    {
        // Verifikasi OTP dari input pengguna
        $record = PasswordReset::where('email', $email)->first();

        if (!$record) {
            return false; // Jika tidak ada email yang sesuai
        }

        // Cek apakah OTP cocok dan belum kadaluarsa
        return $record->token === $otp && !$this->isTokenExpired($record->created_at);
    }

    public function isTokenExpired($createdAt)
    {
        // Set OTP expired setelah 10 menit, bisa diubah sesuai kebutuhan
        return Carbon::parse($createdAt)->addMinutes(10)->isPast();
    }
}