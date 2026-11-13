<script setup>
import { useForm } from '@inertiajs/vue3';
import { ArrowLeft, LockKeyhole } from 'lucide-vue-next';
const props = defineProps({ token: String, email: { type: String, default: '' } });
const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => form.post('/reset-password');
</script>

<template>
<div class="min-h-screen bg-[#F7F9F7] flex items-center justify-center px-5 py-10">
  <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl">
    <a href="/login" class="text-xs font-bold uppercase tracking-wider text-[#006B3C]"><ArrowLeft :size="13" class="inline"/> Back to login</a>
    <p class="mt-8 text-xs font-bold uppercase tracking-widest text-[#006B3C]">BBHS Member Portal</p>
    <h1 class="mt-2 text-3xl font-extrabold text-[#00351F]">Set your password</h1>
    <p class="mt-3 text-sm leading-6 text-gray-500">Choose a secure password for your BBHS account.</p>
    <form class="mt-7 space-y-4" @submit.prevent="submit">
      <div><label class="mb-2 block text-sm font-semibold text-gray-700">Email address</label><input v-model="form.email" type="email" required autocomplete="email" class="w-full rounded-lg border-gray-200 py-3 text-sm"/></div>
      <div><label class="mb-2 block text-sm font-semibold text-gray-700">New password</label><div class="relative"><LockKeyhole :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"/><input v-model="form.password" type="password" required autocomplete="new-password" class="w-full rounded-lg border-gray-200 py-3 pl-10 text-sm"/></div><p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p></div>
      <div><label class="mb-2 block text-sm font-semibold text-gray-700">Confirm password</label><input v-model="form.password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-lg border-gray-200 py-3 text-sm"/></div>
      <p v-if="form.errors.email" class="text-xs text-red-600">{{ form.errors.email }}</p>
      <button :disabled="form.processing" class="w-full rounded-lg bg-[#006B3C] px-5 py-3.5 text-sm font-bold text-white disabled:opacity-50">{{ form.processing ? 'SAVING...' : 'SET PASSWORD' }}</button>
    </form>
  </div>
</div>
</template>
