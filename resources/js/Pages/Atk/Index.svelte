<script context="module">
  import LayoutAlatTulis, { title } from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { inertia } from '@inertiajs/svelte'
  import { onMount } from 'svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'

  onMount(() => {
    pageTitle.set('Permintaan ATK')
  })
</script>

<div class="max-w-4xl">
  <h1 class="mb-2 text-3xl font-bold text-gray-800">Permintaan Alat Tulis Kantor</h1>
  <p class="mb-8 text-gray-500">Ajukan permintaan alat tulis kantor untuk kebutuhan kegiatan tim Anda.</p>

  <div class="grid gap-6 sm:grid-cols-2">
    <!-- Form Pengajuan -->
    <a use:inertia href="/atk/form" class="group flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-400 hover:shadow-md">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white group-hover:bg-indigo-700">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
      </div>
      <div>
        <h2 class="font-semibold text-gray-800 group-hover:text-indigo-700">Form Pengajuan Baru</h2>
        <p class="mt-1 text-sm text-gray-500">Isi formulir untuk mengajukan permintaan alat tulis kantor.</p>
      </div>
    </a>

    <!-- Kembali ke Beranda -->
    <a use:inertia href="/" class="group flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-400 hover:shadow-md">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 group-hover:bg-indigo-100 group-hover:text-indigo-700">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v9m-4-9h16" />
        </svg>
      </div>
      <div>
        <h2 class="font-semibold text-gray-800 group-hover:text-indigo-700">Kembali ke Beranda</h2>
        <p class="mt-1 text-sm text-gray-500">Kembali ke halaman pilihan layanan utama.</p>
      </div>
    </a>
  </div>

  <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50 px-6 py-5">
    <h3 class="mb-1 font-semibold text-indigo-800">Informasi</h3>
    <ul class="list-inside list-disc space-y-1 text-sm text-indigo-700">
      <li>Pengajuan dapat dilakukan oleh seluruh pegawai BPS Kabupaten Magelang.</li>
      <li>Pastikan mengisi nama, tim, dan kegiatan dengan benar.</li>
      <li>Permintaan akan diproses setelah disetujui oleh admin.</li>
    </ul>
  </div>
</div>
