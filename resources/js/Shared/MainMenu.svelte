<script>
  import { inertia, page } from '@inertiajs/svelte'
  import Icon from '@/Shared/Icon.svelte'

  $: isUrl = (...urls) => {
    let currentUrl = $page.url.substr(1)

    if (urls[0] === '') {
      return currentUrl === ''
    }

    return urls.filter((url) => currentUrl.startsWith(url)).length
  }
</script>

<div {...$$restProps}>
  <div class="mb-4">
    <a use:inertia href="/" class="group flex items-center py-3 md:px-2">
      <Icon name="dashboard" class="mr-2 h-4 w-4 {isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'}" />
      <div class={isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Beranda</div>
    </a>
  </div>
  <div class="mb-2 rounded bg-indigo-900 px-3 py-2 sm:px-2">
    <div class="group flex items-start py-1">
      <!-- make the font smaller -->
      <div class="text-xs text-indigo-600">PENGAJUAN NOMOR SURAT</div>
    </div>
    <div class="group flex items-start py-2">
      <a use:inertia href="/surat-tugas">
        <div class={isUrl('surat-tugas') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Surat Tugas</div>
      </a>
    </div>
    <div class="group flex items-start py-2">
      <a use:inertia href="/surat-undangan/form">
        <div class={isUrl('surat-undangan/form') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Surat Undangan</div>
      </a>
    </div>
    <div class="group flex items-start py-2">
      <a use:inertia href="/surat-pengantar/form">
        <div class={isUrl('surat-pengantar/form') ? 'text-white' : 'text-indigo-300 group-hover:text-white'}>Surat Pengantar dll</div>
      </a>
    </div>
  </div>
</div>
