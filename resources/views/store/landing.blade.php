<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="text-sm text-gray-500">Sign in to</p>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $tenant->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Your store link: {{ request()->getSchemeAndHttpHost() }}/app/{{ $tenant->slug }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="tenant_slug" value="{{ $tenant->slug }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-3 mt-6">
            <x-primary-button class="w-full justify-center">{{ __('Log in') }}</x-primary-button>
            <a href="{{ route('login') }}" class="text-center text-sm text-gray-500 hover:text-gray-700">Sign in to main site</a>
        </div>
    </form>
</x-guest-layout>
