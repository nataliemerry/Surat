<script context="module">
  import Layout from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { inertia } from '@inertiajs/svelte'
  import { useForm } from '@inertiajs/svelte'
  import LoadingButton from '@/Shared/LoadingButton.svelte'

  export let teams = []
  export let categories = []
  export let items = []

  const form = useForm({
    requester_name: '',
    team_id: '',
    activity: '',
    items: [{ item_id: '', qty_requested: 1 }],
  })

  function addItem() {
    $form.items = [...$form.items, { item_id: '', qty_requested: 1 }]
  }

  function removeItem(index) {
    $form.items = $form.items.filter((_, i) => i !== index)
  }

  function submit() {
    $form.post('/atk/store')
  }
</script>

<h1 class="mb-8 text-3xl font-bold">
  <a use:inertia href="/" class="text-indigo-400 hover:text-indigo-600">ATK</a>
  <span class="font-medium text-indigo-400">/</span> Permintaan
</h1>

<div class="max-w-3xl overflow-hidden rounded-md bg-white shadow">
  <form on:submit|preventDefault={submit}>
    <div class="-mb-8 -mr-6 flex flex-wrap p-8">
      <div class="w-full pb-8 pr-6">
        <label class="mb-1 block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
        <input type="text" bind:value={$form.requester_name} class="w-full rounded border px-3 py-2" required />
        {#if $form.errors.requester_name}
          <p class="mt-1 text-sm text-red-500">{$form.errors.requester_name}</p>
        {/if}
      </div>

      <div class="w-full pb-8 pr-6">
        <label class="mb-1 block text-sm font-medium text-gray-700">Tim <span class="text-red-500">*</span></label>
        <select bind:value={$form.team_id} class="w-full rounded border px-3 py-2" required>
          <option value="">Pilih Tim</option>
          {#each teams as team}
            <option value={team.id}>{team.name}</option>
          {/each}
        </select>
        {#if $form.errors.team_id}
          <p class="mt-1 text-sm text-red-500">{$form.errors.team_id}</p>
        {/if}
      </div>

      <div class="w-full pb-8 pr-6">
        <label class="mb-1 block text-sm font-medium text-gray-700">Kegiatan <span class="text-red-500">*</span></label>
        <input type="text" bind:value={$form.activity} class="w-full rounded border px-3 py-2" required />
        {#if $form.errors.activity}
          <p class="mt-1 text-sm text-red-500">{$form.errors.activity}</p>
        {/if}
      </div>

      <div class="w-full pb-4 pr-6">
        <label class="mb-2 block text-sm font-medium text-gray-700">Daftar Barang <span class="text-red-500">*</span></label>
        {#each $form.items as item, i}
          <div class="mb-2 flex gap-2">
            <select bind:value={item.item_id} class="flex-1 rounded border px-3 py-2" required>
              <option value="">Pilih Barang</option>
              {#each categories as cat}
                <optgroup label={cat.name}>
                  {#each items.filter((it) => it.category_id === cat.id) as atk_item}
                    <option value={atk_item.id}>{atk_item.name} ({atk_item.satuan})</option>
                  {/each}
                </optgroup>
              {/each}
            </select>
            <input type="number" bind:value={item.qty_requested} min="1" class="w-24 rounded border px-3 py-2" required />
            {#if $form.items.length > 1}
              <button type="button" on:click={() => removeItem(i)} class="rounded bg-red-500 px-3 py-2 text-white hover:bg-red-600">-</button>
            {/if}
          </div>
        {/each}
        <button type="button" on:click={addItem} class="mt-2 rounded bg-indigo-500 px-4 py-2 text-sm text-white hover:bg-indigo-600">+ Tambah Barang</button>
      </div>
    </div>
    <div class="flex items-center justify-end border-t border-gray-100 bg-gray-50 px-8 py-4">
      <LoadingButton loading={$form.processing} class="btn-indigo hover:bg-indigo-700" type="submit">Ajukan Permintaan</LoadingButton>
    </div>
  </form>
</div>
