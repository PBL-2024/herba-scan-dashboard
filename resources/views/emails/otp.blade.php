<!DOCTYPE html>

<html>

<head>
    {{-- tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="container mx-auto">
        <div class="bg-gray-100 rounded-xl px-14 pt-6 pb-8 mb-4">
            <img src="{{ asset('images/logo.png') }}" width="100" alt="" class="mx-auto m-3">
            <div class="bg-white rounded px-8 pt-6 pb-8 mb-4">
                <h1 class="text-2xl font-bold mb-5">OTP Verification</h1>
                <p class="mb-3">Selamat Datang di HerbaScan</p>
                <p class="mb-3">Jika anda tidak merasa melakukan tindakan ini, abaikan email ini.</p>
                <p class="mb-3">Berikut adalah kode OTP untuk verifikasi akun Anda.</p>
                <p>Kode OTP: <strong>{{ $otp }}</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
