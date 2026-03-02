<script>
  import { useForm } from '@inertiajs/svelte'
  import LoadingButton from '@/Shared/LoadingButton.svelte'
  import Logo from '@/Shared/Logo.svelte'
  import TextInput from '@/Shared/TextInput.svelte'

  let form = useForm({
    email: 'johndoe@example.com',
    password: 'secret',
  })

  function login() {
    $form.post('/login')
  }
</script>

<svelte:head>
  <title>Login - BPS Kabupaten Magelang</title>
</svelte:head>

<div class="flex min-h-screen items-center justify-center bg-indigo-800 p-6">
  <div class="w-full max-w-md">
    <Logo class="mx-auto block w-full max-w-xs fill-white" height="50" />
    <form class="mt-8 overflow-hidden rounded-lg bg-white shadow-xl" on:submit|preventDefault={login}>
      <div class="px-10 py-12">
        <TextInput bind:value={$form.email} error={$form.errors.email} class="mt-10" label="Email:" type="email" autofocus autocapitalize="off" />
        <TextInput bind:value={$form.password} class="mt-6" label="Password:" type="password" />
      </div>
      <div class="flex border-t border-gray-100 bg-gray-100 px-10 py-4">
        <LoadingButton bind:loading={$form.processing} class="btn-indigo ml-auto" type="submit">Login</LoadingButton>
      </div>
    </form>
  </div>
</div>
