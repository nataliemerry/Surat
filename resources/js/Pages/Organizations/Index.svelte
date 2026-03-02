<script context="module">
  import Layout, { title } from '@/Shared/Layout.svelte'
  export const layout = Layout
</script>

<script>
  import { inertia, router } from '@inertiajs/svelte'
  import Icon from '@/Shared/Icon.svelte'
  import Pagination from '@/Shared/Pagination.svelte'

  // where this data coming form? 
  export let organizations = []

  $title = 'Organizations'
</script>

<h1 class="mb-4 text-3xl font-bold">Pengajuan Nomor Surat Tugas</h1>

<div class="overflow-x-auto rounded-md bg-white shadow">
  <table>
    <tr class="text-left font-bold">
      <th class="px-6 pb-4 pt-6">No</th>
      <th class="px-6 pb-4 pt-6">Tanggal Surat</th>
      <th class="px-6 pb-4 pt-6">Kode Arsip</th>
      <th class="px-6 pb-4 pt-6">Perihal kegiatan</th>
      <th class="px-6 pb-4 pt-6">Kepada</th>
      <th class="px-6 pb-4 pt-6">Nomor Surat</th>
      <th class="px-6 pb-4 pt-6">Upload Surat</th>
      <th class="px-6 pb-4 pt-6">Keterangan</th>
    </tr>
    {#each organizations.data as organization (organization.id)}
      <tr class="focus-within:bg-gray-100 hover:bg-gray-100">
        <!-- how to add text overflow ellipsis and set max width on this td -->
        <td class="border-t max-w-32 ">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4 text-ellipsis overflow-hidden focus:text-indigo-500">
            {organization.name}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.city || ''}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.phone || ''}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.phone || ''}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.phone || ''}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.phone || ''}
          </a>
        </td>
        <td class="border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-6 py-4" tabindex="-1">
            {organization.name}
          </a>
        </td>
        <td class="w-px border-t">
          <a use:inertia href="/organizations/{organization.id}/edit" class="flex items-center px-4" tabindex="-1">
            <Icon name="cheveron-right" class="block h-6 w-6 fill-gray-400" />
          </a>
        </td>
      </tr>
    {/each}

    {#if organizations.data.length === 0}
      <tr>
        <td class="border-t px-6 py-4" colspan="4">No organizations found.</td>
      </tr>
    {/if}
  </table>
</div>
<Pagination class="mt-6" links={organizations.links} />
