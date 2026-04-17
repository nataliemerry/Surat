<script context="module">
  import LayoutAlatTulis from '@/Shared/LayoutAlatTulis.svelte'
  export const layout = LayoutAlatTulis
</script>

<script>
  import { onMount } from 'svelte'
  import { router, useForm } from '@inertiajs/svelte'
  import { title as pageTitle } from '@/Shared/LayoutAlatTulis.svelte'
  import { Pencil, Trash2, Plus, X, AlertTriangle } from 'lucide-svelte'

  onMount(() => pageTitle.set('Manajemen Pegawai'))

  export let pegawais = []

  let searchQuery = ''

  $: filteredPegawais = pegawais.filter((p) => {
    const search = searchQuery.trim().toLowerCase()
    if (!search) return true
    return p.nama.toLowerCase().includes(search) || (p.nip && p.nip.toLowerCase().includes(search))
  })

  let modal = null
  let editPegawai = null

  const form = useForm({
    nama: '',
    nip: '',
  })

  function openAdd() {
    $form.nama = ''
    $form.nip = ''
    editPegawai = null
    modal = 'form'
  }

  function openEdit(pegawai) {
    editPegawai = pegawai
    $form.nama = pegawai.nama
    $form.nip = pegawai.nip || ''
    modal = 'form'
  }

  function openDelete(pegawai) {
    editPegawai = pegawai
    modal = 'delete'
  }

  function closeModal() {
    modal = null
    editPegawai = null
    form.clearErrors()
  }

  function submit() {
    if (!/^[0-9]{18}$/.test($form.nip) && $form.nip.trim() !== '') {
      $form.errors.nip = 'NIP harus berupa 18 digit angka.'
      return
    } else {
      $form.errors.nip = null
    }

    if (editPegawai) {
      $form.put(`/pegawai/${editPegawai.id}`, { onSuccess: closeModal })
    } else {
      $form.post('/pegawai', { onSuccess: closeModal })
    }
  }

  function confirmDelete() {
    router.delete(`/pegawai/${editPegawai.id}`, { onSuccess: closeModal })
  }
</script>

<div class="w-full">
  <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Manajemen Pegawai</h1>
      <p class="mt-1 text-gray-500">Daftar nama dan NIP pegawai.</p>
    </div>
    <button on:click={openAdd} class="inline-flex w-fit items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
      <Plus class="h-4 w-4" />
      Tambah Pegawai
    </button>
  </div>

  <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 sm:px-5">
      <div class="relative w-full max-w-sm">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" /><path stroke-linecap="round" d="m21 21-4.35-4.35" />
        </svg>
        <input type="search" bind:value={searchQuery} placeholder="Cari nama atau NIP…" class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
      </div>
    </div>

    {#if filteredPegawais.length === 0}
      <div class="py-14 text-center text-gray-400">
        <p class="text-sm">Tidak ada pegawai yang ditemukan.</p>
      </div>
    {:else}
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
            <tr>
              <th class="px-5 py-3 text-left">Nama</th>
              <th class="px-5 py-3 text-left">NIP</th>
              <th class="px-5 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            {#each filteredPegawais as pegawai (pegawai.id)}
              <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{pegawai.nama}</td>
                <td class="px-5 py-3 text-gray-600">{pegawai.nip || '-'}</td>
                <td class="px-5 py-3">
                  <div class="flex items-center justify-center gap-2">
                    <button on:click={() => openEdit(pegawai)} class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                      <Pencil class="h-3.5 w-3.5" />
                      Edit
                    </button>
                    <button on:click={() => openDelete(pegawai)} class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50">
                      <Trash2 class="h-3.5 w-3.5" />
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {/if}
  </div>
</div>

<!-- Modal Form -->
{#if modal === 'form'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 sm:px-0">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h3 class="text-lg font-bold text-gray-800">{editPegawai ? 'Edit Pegawai' : 'Tambah Pegawai'}</h3>
        <button on:click={closeModal} class="rounded-full p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
          <X class="h-5 w-5" />
        </button>
      </div>
      <form on:submit|preventDefault={submit} class="px-5 py-4">
        <div class="space-y-4">
          <div>
            <label for="nama" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" bind:value={$form.nama} class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-1 {$form.errors.nama ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'}" placeholder="Masukkan nama pegawai" required />
            {#if $form.errors.nama}
              <p class="mt-1 text-xs text-red-600">{$form.errors.nama}</p>
            {/if}
          </div>
          <div>
            <label for="nip" class="mb-1.5 block text-sm font-medium text-gray-700">NIP</label>
            <input id="nip" type="text" name="nip" bind:value={$form.nip} class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-1 {$form.errors.nip ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'}" placeholder="Contoh: 199001012020011001 (tanpa spasi)" />
            {#if $form.errors.nip}
              <p class="mt-1 text-xs text-red-600">{$form.errors.nip}</p>
            {/if}
          </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
          <button type="button" on:click={closeModal} class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"> Batal </button>
          <button type="submit" disabled={$form.processing} class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70">
            {$form.processing ? 'Menyimpan...' : 'Simpan'}
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}

<!-- Modal Hapus -->
{#if modal === 'delete'}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 sm:px-0">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
      <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
        <AlertTriangle class="h-6 w-6 text-red-600" />
      </div>
      <h3 class="mb-2 text-lg font-bold text-gray-800">Hapus Pegawai</h3>
      <p class="mb-6 text-sm text-gray-500">
        Anda yakin ingin menghapus "<strong>{editPegawai?.nama}</strong>"? Tindakan ini tidak dapat dibatalkan.
      </p>
      <div class="flex gap-3">
        <button on:click={closeModal} class="flex-1 rounded-lg border border-gray-300 bg-white py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"> Batal </button>
        <button on:click={confirmDelete} class="flex-1 rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700"> Hapus </button>
      </div>
    </div>
  </div>
{/if}
