<script>
  import { inertia } from '@inertiajs/svelte'
  import Logo from '@/Shared/Logo.svelte'
  import Dropdown from '@/Shared/Dropdown.svelte'
  import Icon from '@/Shared/Icon.svelte'
  import { MailOpen, NotebookPen } from 'lucide-svelte'
  let auth = null
</script>

<svelte:head>
  <title>BPS Kabupaten Magelang - Sistem Layanan Digital</title>
</svelte:head>

<div class="min-h-screen bg-indigo-800 md:flex md:flex-col">
  <div class="md:flex md:h-screen md:flex-col">
    <div class="md:flex md:shrink-0">
      <div class="flex w-full items-center justify-between bg-indigo-900 px-6 py-4">
        <a use:inertia href="/" class="mt-1">
          <Logo class="fill-white" width="120" height="28" />
        </a>
        {#if auth && auth.user}
          <Dropdown class="mt-1" placement="bottom-end">
            <div class="group flex cursor-pointer select-none items-center">
              <div class="mr-1 whitespace-nowrap text-gray-700 focus:text-indigo-600 group-hover:text-indigo-600">
                <span>{auth.user.first_name}</span>
                <span class="hidden md:inline">{auth.user.last_name}</span>
              </div>
              <Icon name="cheveron-down" class="h-5 w-5 fill-gray-700 focus:fill-indigo-600 group-hover:fill-indigo-600" />
            </div>
            <div slot="dropdown" class="mt-2 rounded bg-white py-2 text-sm shadow-xl">
              <a use:inertia href="/users/{auth.user.id}/edit" class="block px-6 py-2 hover:bg-indigo-500 hover:text-white"> My Profile </a>
              <a use:inertia href="/users" class="block px-6 py-2 hover:bg-indigo-500 hover:text-white"> Manage Users </a>
              <button use:inertia={{ href: '/logout', method: 'delete' }} class="block w-full px-6 py-2 text-left hover:bg-indigo-500 hover:text-white"> Logout </button>
            </div>
          </Dropdown>
        {:else}{/if}
      </div>
    </div>
    <div class="mt-6 flex w-full flex-col items-center justify-start py-4 sm:mt-20">
      <div class="mb-2 text-center">
        <div class="text-2xl font-bold text-white sm:text-4xl">Sistem Layanan Digital</div>
        <div class="text-lg font-semibold text-indigo-200 sm:text-2xl">BPS Kabupaten Magelang</div>
      </div>
      <div class="mt-4 grid w-full max-w-xs gap-4 sm:mt-10 sm:max-w-2xl sm:grid-cols-2 sm:gap-6">
        <a use:inertia href="/surat" class="group flex flex-col items-center justify-center rounded-2xl bg-white px-4 py-6 text-center shadow-xl transition hover:bg-indigo-50 hover:shadow-2xl sm:px-8 sm:py-10">
          <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 group-hover:bg-indigo-200 sm:mb-4 sm:h-16 sm:w-16">
            <MailOpen class="h-6 w-6 text-indigo-700 sm:h-8 sm:w-8" />
          </div>
          <h2 class="text-base font-bold text-gray-800 sm:text-lg">Penomoran Surat Otomatis</h2>
          <p class="mt-2 text-xs text-gray-500 sm:text-base">Ajukan permintaan nomor surat secara otomatis.</p>
        </a>
        <a use:inertia href="/atk" class="group flex flex-col items-center justify-center rounded-2xl bg-white px-4 py-6 text-center shadow-xl transition hover:bg-indigo-50 hover:shadow-2xl sm:px-8 sm:py-10">
          <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 group-hover:bg-indigo-200 sm:mb-4 sm:h-16 sm:w-16">
            <NotebookPen class="h-6 w-6 text-indigo-700 sm:h-8 sm:w-8" />
          </div>
          <h2 class="text-base font-bold text-gray-800 sm:text-lg">Permintaan Alat Tulis Kantor</h2>
          <p class="mt-2 text-xs text-gray-500 sm:text-base">Ajukan permintaan alat tulis kantor untuk kebutuhan Anda.</p>
        </a>
      </div>
    </div>
  </div>
</div>
