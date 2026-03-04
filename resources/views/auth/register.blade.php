<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Shop Details -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Business Information</h3>

            <div>
                <x-input-label for="shop_name" :value="__('Shop / Business Name')" />
                <x-text-input id="shop_name" class="block mt-1 w-full" type="text" name="shop_name"
                    :value="old('shop_name')" required placeholder="e.g. Green Groceries" />
                <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="shop_address" :value="__('Shop Address')" />
                <x-text-input id="shop_address" class="block mt-1 w-full" type="text" name="shop_address"
                    :value="old('shop_address')" required />
                <x-input-error :messages="$errors->get('shop_address')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="shop_phone" :value="__('Contact Number')" />
                <x-text-input id="shop_phone" class="block mt-1 w-full" type="text" name="shop_phone"
                    :value="old('shop_phone')" />
                <x-input-error :messages="$errors->get('shop_phone')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>