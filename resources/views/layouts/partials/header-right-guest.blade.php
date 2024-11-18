<div class="flex space-x-5">
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <x-nav-link  href="{{ route('login') }}" :active="request()->routeIs('login')">
            {{ __('Login') }}
        </x-nav-link>
    </div>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
            {{ __('Reister') }}
        </x-nav-link>
    </div>
</div>
