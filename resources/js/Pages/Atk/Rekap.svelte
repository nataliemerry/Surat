<script context="module">
  import LayoutAlatTulis from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { onMount } from 'svelte'
  import { router } from '@inertiajs/svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'

  onMount(() => pageTitle.set('Rekap Permintaan ATK'))

  export let rekap = []
  export let year = new Date().getFullYear()
  export let month = new Date().getMonth() + 1
  export let years = []

  const bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

  function reload() {
    router.get('/atk/rekap', { year, month }, { preserveState: true, replace: true })
  }

  $: totalRequested = rekap.reduce((s, r) => s + Number(r.total_requested), 0)
  $: totalApproved = rekap.reduce((s, r) => s + Number(r.total_approved), 0)
</script>

<div class="w-full">
  <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Rekap Permintaan ATK</h1>
      <p class="mt-1 text-gray-500">Rekapitulasi jumlah permintaan barang per bulan.</p>
    </div>
  </div>

  <!-- Filter -->
  <div class="flex flex-wrap items-center gap-3 p-4 mb-6 bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="flex items-center gap-2">
      <label class="text-sm font-medium text-gray-700 shrink-0">Bulan</label>
      <select bind:value={month} on:change={reload} class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        {#each Array.from({ length: 12 }, (_, i) => i + 1) as m}
          <option value={m}>{bulanNames[m]}</option>
        {/each}
      </select>
    </div>
    <div class="flex items-center gap-2">
      <label class="text-sm font-medium text-gray-700 shrink-0">Tahun</label>
      <select bind:value={year} on:change={reload} class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        {#each years as y}
          <option value={y}>{y}</option>
        {/each}
      </select>
    </div>
  </div>

  {#if rekap.length === 0}
    <div class="text-center text-gray-400 border border-gray-300 border-dashed rounded-xl py-14">
      <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />
      </svg>
      <p class="text-sm">Tidak ada data permintaan untuk {bulanNames[month]} {year}.</p>
    </div>
  {:else}
    <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-xl">
      <table class="min-w-full text-sm">
        <thead class="text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50">
          <tr>
            <th class="w-8 px-4 py-3 text-left">No</th>
            <th class="px-4 py-3 text-left">Nama Barang</th>
            <th class="px-4 py-3 text-left">Kategori</th>
            <th class="px-4 py-3 text-left">Satuan</th>
            <th class="px-4 py-3 text-center">Total Diminta</th>
            <th class="px-4 py-3 text-center">Total Disetujui</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          {#each rekap as row, i}
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-400">{i + 1}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{row.item_name}</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                  {row.category_name}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-600">{row.satuan}</td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                  {row.total_requested}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                  {row.total_approved}
                </span>
              </td>
            </tr>
          {/each}
        </tbody>
        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
          <tr>
            <td colspan="4" class="px-4 py-3 text-sm font-semibold text-right text-gray-700">Total</td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                {totalRequested}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-bold text-green-800">
                {totalApproved}
              </span>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  {/if}
</div>
