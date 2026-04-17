<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-0 space-y-4">
        @csrf
        @method('patch')

        @php
            $anggota = $user->anggota;
            $isMale = old('jenis_kelamin', $anggota?->jenis_kelamin) === 'L';
            $isFemale = old('jenis_kelamin', $anggota?->jenis_kelamin) === 'P';
        @endphp

        @if (session('success'))
            <div class="rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc ps-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <x-input-label for="name" :value="__('Nama')" class="-mt-3" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $anggota?->nama ?? $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="nip" :value="__('NIP')" class="-mt-3" />
                <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip', $anggota?->nip ?? '')" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('nip')" />
            </div>

            <div>
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected($isMale)>Laki-laki</option>
                    <option value="P" @selected($isFemale)>Perempuan</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="md:col-span-2">
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Email belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Link verifikasi telah dikirim ulang ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <x-input-label :value="__('Nomor Anggota')" />
                <x-text-input type="text" class="mt-1 block w-full bg-gray-100 text-gray-500" :value="$anggota?->nomor_anggota ?? '-'" disabled />
            </div>

            <div>
                <x-input-label :value="__('Status')" />
                <x-text-input type="text" class="mt-1 block w-full bg-gray-100 text-gray-500" :value="ucfirst($anggota?->status ?? '-')" disabled />
            </div>

            <div>
                <x-input-label :value="__('Jabatan')" />
                <x-text-input type="text" class="mt-1 block w-full bg-gray-100 text-gray-500" :value="$anggota?->jabatan ?? '-'" disabled />
            </div>

            <div>
                <x-input-label :value="__('Tanggal Masuk')" />
                <x-text-input type="text" class="mt-1 block w-full bg-gray-100 text-gray-500" :value="$anggota?->tanggal_masuk ? \Carbon\Carbon::parse($anggota->tanggal_masuk)->translatedFormat('d F Y') : '-'" disabled />
            </div>
        </div>

        <div class="pt-2 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <x-input-label for="current_password" :value="__('Password Saat Ini')" />
                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password Baru')" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
        </div>
    </form>
</section>
