<x-layouts.app :title="__('Dashboard')">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
      <div
        class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-zinc-900"
      >
        <div class="flex h-full w-full flex-col justify-between">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Welcome back
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              {{ auth()->user()->name }}
            </p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-300">
              {{ auth()->user()->email }}
            </p>
          </div>

          <div class="mt-6 flex flex-wrap gap-3">
            <a
              href="{{ route('profile.edit') }}"
              wire:navigate
              class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-white/90"
            >
              Manage Profile
            </a>
            <a
              href="{{ route('dashboard') }}"
              class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
              View Activity
            </a>
          </div>
        </div>
      </div>

      <div
        class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900"
      >
        <x-placeholder-pattern
          class="absolute inset-0 size-full stroke-zinc-900/10 dark:stroke-neutral-100/10"
        />

        <div class="relative flex h-full w-full flex-col justify-between">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Quick Links
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              Customize your starter kit experience
            </p>
          </div>

          <div class="mt-6 flex flex-wrap gap-3">
            <a
              href="{{ route('profile.edit') }}"
              wire:navigate
              class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-white/90"
            >
              Update Profile
            </a>

            <a
              href="https://laravel.com/docs/starter-kits#livewire"
              target="_blank"
              rel="noopener noreferrer"
              class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
              Browse Documentation
            </a>
          </div>
        </div>
      </div>

      <div
        class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900"
      >
        <x-placeholder-pattern
          class="absolute inset-0 size-full stroke-zinc-900/10 dark:stroke-neutral-100/10"
        />

        <div class="relative flex h-full w-full flex-col justify-between p-6">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Onboarding Checklist
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              Next steps to explore
            </p>
          </div>

          <ul class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-200">
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-emerald-500"></span>
              <span>Invite your team members and assign roles.</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-emerald-500"></span>
              <span>Configure appearance preferences in Settings.</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-emerald-500"></span>
              <span>Review the example Filament resources bundled in the kit.</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
      <div
        class="relative min-h-[220px] overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900"
      >
        <x-placeholder-pattern
          class="absolute inset-0 size-full stroke-zinc-900/10 dark:stroke-neutral-100/10"
        />

        <div class="relative flex h-full flex-col justify-between">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Filament Toolkit
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              Build admin experiences faster
            </p>
          </div>

          <div class="mt-6 flex flex-wrap gap-3">
            <a
              href="https://filamentphp.com/docs"
              target="_blank"
              rel="noopener noreferrer"
              class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-white/90"
            >
              Filament Docs
            </a>
            <a
              href="https://github.com/filamentphp/filament"
              target="_blank"
              rel="noopener noreferrer"
              class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
              GitHub
            </a>
          </div>
        </div>
      </div>

      <div
        class="relative min-h-[220px] overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900"
      >
        <x-placeholder-pattern
          class="absolute inset-0 size-full stroke-zinc-900/10 dark:stroke-neutral-100/10"
        />

        <div class="relative flex h-full flex-col justify-between">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Starter Kit Highlights
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              Curated resources to keep handy
            </p>
          </div>

          <ul class="mt-6 space-y-2 text-sm text-zinc-600 dark:text-zinc-200">
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-sky-500"></span>
              <a
                href="https://laravel.com/docs"
                class="hover:text-zinc-900 dark:hover:text-white"
                target="_blank"
                rel="noopener noreferrer"
              >
                Laravel Documentation
              </a>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-sky-500"></span>
              <a
                href="https://laravel.com/docs/starter-kits#livewire"
                class="hover:text-zinc-900 dark:hover:text-white"
                target="_blank"
                rel="noopener noreferrer"
              >
                Livewire Starter Kit Guide
              </a>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-sky-500"></span>
              <a
                href="https://fluxui.dev"
                class="hover:text-zinc-900 dark:hover:text-white"
                target="_blank"
                rel="noopener noreferrer"
              >
                Flux UI Components
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div
        class="relative min-h-[220px] overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900"
      >
        <x-placeholder-pattern
          class="absolute inset-0 size-full stroke-zinc-900/10 dark:stroke-neutral-100/10"
        />

        <div class="relative flex h-full flex-col justify-between">
          <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-300">
              Helpful Tips
            </p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
              Keep your workflow humming
            </p>
          </div>

          <ul class="mt-6 space-y-2 text-sm text-zinc-600 dark:text-zinc-200">
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-amber-500"></span>
              Run <code class="rounded bg-zinc-100 px-1 py-0.5 dark:bg-zinc-800">bun run dev</code> to watch frontend changes.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-amber-500"></span>
              Invite collaborators via <code class="rounded bg-zinc-100 px-1 py-0.5 dark:bg-zinc-800">php artisan user:invite</code>.
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 size-1.5 rounded-full bg-amber-500"></span>
              Explore the example tests in <code class="rounded bg-zinc-100 px-1 py-0.5 dark:bg-zinc-800">tests/Feature</code> for coverage patterns.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</x-layouts.app>
