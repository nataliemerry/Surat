<script context="module">
  import LayoutAlatTulis, { title } from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { inertia, useForm, page } from '@inertiajs/svelte'
  import { onMount } from 'svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'

  onMount(() => {
    pageTitle.set('Riwayat Permintaan ATK')
  })

  export let requests = []

  $: auth = $page.props.auth
  $: isAdmin = auth && auth.user

  let activeTab = 'pending'

  $: pendingList = requests.filter((r) => r.status === 'pending')
  $: approvedList = requests.filter((r) => r.status === 'approved')
  $: tabList = activeTab === 'pending' ? pendingList : approvedList

  let detailOpen = false
  let detailRequest = null

  function openDetail(r) {
    detailRequest = r
    detailOpen = true
  }

  function closeDetail() {
    detailOpen = false
    detailRequest = null
  }

  let reviewOpen = false
  let reviewRequest = null
  let approveForm = null

  function openReview(r) {
    reviewRequest = r
    approveForm = useForm({
      items: r.items.map((i) => ({
        id: i.id,
        qty_approved: i.qty_approved ?? i.qty_requested,
      })),
    })
    reviewOpen = true
  }

  function closeReview() {
    reviewOpen = false
    reviewRequest = null
    approveForm = null
  }

  function submitApprove() {
    $approveForm.put(`/atk/${reviewRequest.id}/approve`, {
      onSuccess: () => {
        closeReview()
        activeTab = 'approved'
      },
    })
  }
</script>

<div class="w-full">
  <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Riwayat Permintaan ATK</h1>
    </div>
    <a use:inertia href="/atk/form" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow w-fit hover:bg-indigo-700">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
      Ajukan Permintaan
    </a>
  </div>

  <div class="flex gap-1 mb-4 border-b border-gray-200">
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeTab === 'pending' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => (activeTab = 'pending')}>
      Menunggu
      {#if pendingList.length > 0}
        <span class="ml-1.5 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700">{pendingList.length}</span>
      {/if}
    </button>
    <button class="px-5 py-2.5 text-sm font-medium transition-colors {activeTab === 'approved' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'}" on:click={() => (activeTab = 'approved')}>
      Disetujui
      {#if approvedList.length > 0}
        <span class="ml-1.5 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">{approvedList.length}</span>
      {/if}
    </button>
  </div>

  {#if tabList.length === 0}
    <div class="text-center text-gray-400 border border-gray-300 border-dashed rounded-xl py-14">
      <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z" />
      </svg>
      <p class="text-sm">Belum ada permintaan di tab ini.</p>
    </div>
  {:else}
    <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-xl">
      <table class="min-w-full text-sm">
        <thead class="text-xs font-semibold tracking-wide text-gray-800 uppercase bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Nama Pemohon</th>
            <th class="px-4 py-3 text-left">Tim Kerja</th>
            <th class="px-4 py-3 text-left">Kegiatan</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          {#each tabList as req (req.id)}
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{req.created_at}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{req.pegawai?.nama ?? '-'} </td>
              <td class="px-4 py-3 text-gray-600">{req.team ? req.team.name : '-'}</td>
              <td class="px-4 py-3 text-gray-600">{req.activity}</td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <button on:click={() => openDetail(req)} class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <span class="block sm:hidden">Detail</span>
                    <span class="hidden sm:block">Lihat Detail</span>
                  </button>
                  {#if isAdmin && req.status === 'pending'}
                    <button on:click={() => openReview(req)} class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"> Review </button>
                  {/if}
                  {#if isAdmin && req.status === 'approved'}
                    <a href={`/atk/${req.id}/download`} class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 3v12" />
                      </svg>
                      Excel
                    </a>
                  {/if}
                </div>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  {/if}
</div>

{#if detailOpen && detailRequest}
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" role="dialog" aria-modal="true">
    <div class="flex max-h-[90vh] w-full max-w-lg flex-col rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Detail Permintaan</h2>
        <button on:click={closeDetail} class="p-1 text-gray-400 rounded hover:bg-gray-100 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="flex-1 px-6 py-5 space-y-4 overflow-y-auto">
        <div class="px-4 py-3 space-y-1 text-base rounded-lg bg-gray-50">
          <div class="flex gap-2">
            <span class="w-32 text-gray-800 shrink-0">Nama Pemohon</span>
            <span class="font-medium text-gray-800">{detailRequest.pegawai?.nama ?? '-'} </span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-800 shrink-0">Tim Kerja</span>
            <span class="font-medium text-gray-800">{detailRequest.team ? detailRequest.team.name : '-'}</span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-800 shrink-0">Kegiatan</span>
            <span class="font-medium text-gray-800">{detailRequest.activity}</span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-800 shrink-0">Tanggal</span>
            <span class="font-medium text-gray-800">{detailRequest.created_at}</span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-800 shrink-0">Status</span>
            {#if detailRequest.status === 'approved'}
              <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Disetujui</span>
            {:else}
              <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">Menunggu</span>
            {/if}
          </div>
        </div>

        <div>
          <p class="mb-2 text-sm font-semibold text-gray-700">Daftar Barang</p>
          <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left">Barang</th>
                  <th class="px-3 py-2 text-center">Diajukan</th>
                  {#if detailRequest.status === 'approved'}
                    <th class="px-3 py-2 text-center">Disetujui</th>
                  {/if}
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                {#each detailRequest.items as ri (ri.id)}
                  <tr>
                    <td class="px-3 py-2 text-gray-800">
                      {ri.item ? ri.item.name : '-'}
                      {#if ri.item && ri.item.satuan}
                        <span class="ml-1 text-xs text-gray-400">({ri.item.satuan})</span>
                      {/if}
                    </td>
                    <td class="px-3 py-2 text-center text-gray-600">{ri.qty_requested}</td>
                    {#if detailRequest.status === 'approved'}
                      <td class="px-3 py-2 font-semibold text-center text-green-700">{ri.qty_approved ?? '-'}</td>
                    {/if}
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between px-6 py-4 border-t">
        <div>
          {#if detailRequest.status === 'approved'}
            <a href={`/atk/${detailRequest.id}/download`} class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 3v12" />
              </svg>
              Download Excel
            </a>
          {/if}
        </div>
        <button on:click={closeDetail} class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Tutup</button>
      </div>
    </div>
  </div>
{/if}

{#if reviewOpen && reviewRequest && approveForm}
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" role="dialog" aria-modal="true">
    <div class="flex max-h-[90vh] w-full max-w-lg flex-col rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Review Permintaan</h2>
        <button on:click={closeReview} class="p-1 text-gray-400 rounded hover:bg-gray-100 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="flex-1 px-6 py-5 space-y-4 overflow-y-auto">
        <div class="px-4 py-3 space-y-1 text-sm rounded-lg bg-gray-50">
          <div class="flex gap-2">
            <span class="w-32 text-gray-500 shrink-0">Nama Pemohon</span>
            <span class="font-medium text-gray-800">{reviewRequest.pegawai?.nama ?? '-'} </span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-500 shrink-0">Tim Kerja</span>
            <span class="font-medium text-gray-800">{reviewRequest.team ? reviewRequest.team.name : '-'}</span>
          </div>
          <div class="flex gap-2">
            <span class="w-32 text-gray-500 shrink-0">Kegiatan</span>
            <span class="font-medium text-gray-800">{reviewRequest.activity}</span>
          </div>
        </div>

        <div>
          <p class="mb-2 text-sm font-semibold text-gray-700">Jumlah Barang Disetujui</p>
          <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full text-sm">
              <thead class="text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left">Barang</th>
                  <th class="px-3 py-2 text-center">Diajukan</th>
                  <th class="px-3 py-2 text-center">Disetujui</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                {#each reviewRequest.items as ri, idx (ri.id)}
                  <tr>
                    <td class="px-3 py-2 text-gray-800">
                      {ri.item ? ri.item.name : '-'}
                      {#if ri.item && ri.item.satuan}
                        <span class="ml-1 text-xs text-gray-400">({ri.item.satuan})</span>
                      {/if}
                    </td>
                    <td class="px-3 py-2 text-center text-gray-500">{ri.qty_requested}</td>
                    <td class="px-3 py-2 text-center">
                      <input type="number" min="0" max={ri.qty_requested} bind:value={$approveForm.items[idx].qty_approved} class="w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded-md focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    </td>
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>
        </div>

        {#if $approveForm.errors && Object.keys($approveForm.errors).length > 0}
          <p class="text-sm text-red-600">Terjadi kesalahan, silakan periksa input.</p>
        {/if}
      </div>

      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t">
        <button on:click={closeReview} class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200" disabled={$approveForm.processing}> Batal </button>
        <button on:click={submitApprove} disabled={$approveForm.processing} class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 disabled:opacity-60">
          {$approveForm.processing ? 'Menyimpan…' : 'Setujui Permintaan'}
        </button>
      </div>
    </div>
  </div>
{/if}
