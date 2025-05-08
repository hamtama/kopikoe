<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 pooortext-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 pooortext-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat')" />
            <x-text-input id="alamat" name="alamat" type="text" class="mt-1 block w-full" :value="old('alamat', $user->alamat)"
                required autocomlete="alamat" />
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>

        <div>
            <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
            <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full"
                :value="old('tanggal_lahir', $user->tanggal_lahir)" required autocomplete="tanggal_lahir" />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 pooortext-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 pooortext-gray-400 hover:text-gray-900 pooorhover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 pooorfocus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 pooortext-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Input Kontak -->
        <div>
            <x-input-label for="contact_number" :value="__('Contact Number')" />
            <x-text-input id="no_telepon" name="no_telepon" type="text" class="mt-1 block w-full" :value="old('no_telepon', $user->no_telepon)"
                required autocomplete="tel" placeholder="62XXXXXXXXXX" pattern="^(\+62|62|08)[0-9]{9,13}$" />
            <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
        </div>

        <!-- Input Sosial Media -->
        <div>
            <x-input-label for="social_media" :value="__('Social Media Accounts')" />
            <div class="mt-2 space-y-4">
                <!-- Instagram -->
                <div class="flex items-center">
                    <input type="checkbox" id="instagram_checkbox" name="social_media[]" value="instagram"
                        class="form-checkbox h-5 w-5 text-orange-500" onchange="toggleInput('instagram')"
                        {{ isset($socialMediaAccounts['instagram']) ? 'checked' : '' }}>
                    <label for="instagram_checkbox" class="ml-2 text-sm text-gray-700">{{ __('Instagram') }}</label>
                </div>
                <x-text-input id="instagram_input" name="instagram_account" type="text"
                    class="mt-1 block w-full {{ isset($socialMediaAccounts['instagram']) ? '' : 'hidden' }}"
                    placeholder="Enter Instagram username" value="{{ $socialMediaAccounts['instagram'] ?? '' }}" />

                <!-- Facebook -->
                <div class="flex items-center">
                    <input type="checkbox" id="facebook_checkbox" name="social_media[]" value="facebook"
                        class="form-checkbox h-5 w-5 text-orange-500" onchange="toggleInput('facebook')"
                        {{ isset($socialMediaAccounts['facebook']) ? 'checked' : '' }}>
                    <label for="facebook_checkbox" class="ml-2 text-sm text-gray-700">{{ __('Facebook') }}</label>
                </div>
                <x-text-input id="facebook_input" name="facebook_account" type="text"
                    class="mt-1 block w-full {{ isset($socialMediaAccounts['facebook']) ? '' : 'hidden' }}"
                    placeholder="Enter Facebook username" value="{{ $socialMediaAccounts['facebook'] ?? '' }}" />

                <!-- Twitter -->
                <div class="flex items-center">
                    <input type="checkbox" id="twitter_checkbox" name="social_media[]" value="twitter"
                        class="form-checkbox h-5 w-5 text-orange-500" onchange="toggleInput('twitter')"
                        {{ isset($socialMediaAccounts['twitter']) ? 'checked' : '' }}>
                    <label for="twitter_checkbox" class="ml-2 text-sm text-gray-700">{{ __('Twitter') }}</label>
                </div>
                <x-text-input id="twitter_input" name="twitter_account" type="text"
                    class="mt-1 block w-full {{ isset($socialMediaAccounts['twitter']) ? '' : 'hidden' }}"
                    placeholder="Enter Twitter username" value="{{ $socialMediaAccounts['twitter'] ?? '' }}" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 pooortext-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
<script>
    function toggleInput(platform) {
        const input = document.getElementById(platform + '_input');
        if (document.getElementById(platform + '_checkbox').checked) {
            input.classList.remove('hidden');
        } else {
            input.classList.add('hidden');
            input.value = ''; // Clear input when hidden
        }
    }
</script>
