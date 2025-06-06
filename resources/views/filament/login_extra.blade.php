@if ($errors->has('email'))
    <div class="mb-4 text-sm text-red-600 dark:text-red-400">
        {{ $errors->first('email') }}
    </div>
@endif

<div class="absolute bottom-4 left-4 z-50">
    <div class="flex flex-col leading-tight font-semibold font-poppins">
        <div id="title" class="font-bold text-xl text-gray-900 dark:text-white">Sistem Pembantu</div>
        <div id="slogan" class="text-sm text-gray-500 dark:text-white">Proses Seleksi Penerima Bantuan Sosial</div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/login.css') }}">