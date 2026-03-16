<script>
  import { inertia, page } from '@inertiajs/svelte'
  import { PenBox, LayoutDashboard, Boxes } from 'lucide-svelte'

  $: isUrl = (...urls) => {
    let currentUrl = $page.url.substr(1)
    if (urls[0] === '') {
      return currentUrl === ''
    }
    return urls.filter((url) => currentUrl.startsWith(url)).length
  }

  $: isAdmin = $page.props?.auth?.user?.owner
</script>

<div {...$$restProps}>
  <div class="mb-4">
    <a use:inertia href="/atk" class="flex items-center py-3 group md:px-2">
      <LayoutDashboard class="mr-2 h-4 w-4 {isUrl('atk') ? 'text-white' : 'text-indigo-400 group-hover:text-white'}" />
      <div class={isUrl('atk') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Beranda</div>
    </a>
  </div>
  <div class="px-3 py-2 mb-2 bg-indigo-900 rounded sm:px-2">
    <div class="flex items-start py-1 group">
      <div class="text-xs text-indigo-600">PENGAJUAN ALAT TULIS</div>
    </div>
    <div class="flex items-start py-2 group">
      <a use:inertia href="/atk/form">
        <div class={isUrl('atk/form') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Form Pengajuan</div>
      </a>
    </div>
    {#if isAdmin}
      <div class="flex items-start py-2 group">
        <a use:inertia href="/atk/barang">
          <div class={isUrl('atk/barang') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>
            Kelola Barang
          </div>
        </a>
      </div>
      <div class="flex items-start py-2 group">
        <a use:inertia href="/atk/rekap">
          <div class={isUrl('atk/rekap') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>
            Rekap Bulanan
          </div>
        </a>
      </div>
    {/if}
  </div>
</div>
