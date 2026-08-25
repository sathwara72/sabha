<x-layouts.app :title="__('site.directory.title') . ' | Sabha'" :description="__('site.directory.subtitle')">
    @guest
        <div class="bg-background font-outfit">
            <div class="mx-auto flex min-h-[60vh] max-w-md flex-col items-center justify-center px-6 py-20 text-center">
                <div class="glass-card w-full p-10">
                    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-soft text-primary">
                        <x-icon name="lock" class="h-6 w-6" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Members only</h1>
                    <p class="mx-auto mt-2 max-w-xs text-sm text-muted">
                        Log in to browse the business directory and connect with members.
                    </p>
                    <button type="button" x-on:click="$store.auth.openLogin()" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer">
                        Log in to continue
                    </button>
                    <p class="mt-4 text-sm text-muted">
                        Don't have an account?
                        <button type="button" x-on:click="$store.auth.openRegister()" class="font-semibold text-primary hover:opacity-80 cursor-pointer">Create one</button>
                    </p>
                </div>
            </div>
        </div>
    @else
        @livewire('businesses.directory')
    @endguest
</x-layouts.app>
