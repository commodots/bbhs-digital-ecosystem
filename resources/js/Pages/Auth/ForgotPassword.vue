<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Mail } from 'lucide-vue-next';
const page = usePage();
const form = useForm({ email: '' });
const submit = () => form.post('/forgot-password');
</script>

<template>
<div class="min-h-screen bg-[#F7F9F7] flex items-center justify-center px-5 py-10">
  <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl">
    <a href="/login" class="text-xs font-bold uppercase tracking-wider text-[#006B3C]"><ArrowLeft :size="13" class="inline"/> Back to login</a>
    <p class="mt-8 text-xs font-bold uppercase tracking-widest text-[#006B3C]">BBHS Member Portal</p>
    <h1 class="mt-2 text-3xl font-extrabold text-[#00351F]">Forgot password?</h1>
    <p class="mt-3 text-sm leading-6 text-gray-500">Enter your registered email address. If it belongs to an account, we'll send a secure password setup or recovery link.</p>
    <div v-if="page.props.flash?.success" class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
    <form class="mt-7 space-y-4" @submit.prevent="submit">
      <label class="block text-sm font-semibold text-gray-700">Email address</label>
      <div class="relative"><Mail :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"/><input v-model="form.email" type="email" required autocomplete="email" class="w-full rounded-lg border-gray-200 py-3 pl-10 text-sm" placeholder="you@example.com"/></div>
      <p v-if="form.errors.email" class="text-xs text-red-600">{{ form.errors.email }}</p>
      <button :disabled="form.processing" class="w-full rounded-lg bg-[#006B3C] px-5 py-3.5 text-sm font-bold text-white disabled:opacity-50">{{ form.processing ? 'SENDING...' : 'EMAIL PASSWORD LINK' }}</button>
    </form>
  </div>
</div>
</template>
