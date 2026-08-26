<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, Mail } from 'lucide-vue-next';

const page = usePage();
const showPassword = ref(false);
const form = useForm({ email: '', password: '', remember: false });

const submit = () => form.post('/login');
</script>

<template>
<div class="min-h-screen bg-[#F7F9F7]">
    <div class="mx-auto flex min-h-screen max-w-[1200px] items-center justify-center px-5 py-10">
        <div class="grid w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl lg:grid-cols-2">
            <div class="hidden bg-[#00351F] p-10 lg:flex lg:flex-col lg:justify-between">
                <div>
                    <a href="/" class="text-sm font-bold text-white">BBHS OLD BOYS ASSOCIATION</a>
                    <h1 class="mt-20 text-4xl font-extrabold leading-tight text-white">Welcome back to the <span class="text-[#16A34A]">Brotherhood.</span></h1>
                    <p class="mt-5 text-sm leading-7 text-white/70">Sign in to access your BBHS member account, profile, events and alumni community.</p>
                </div>
                <p class="text-xs text-white/40">United by Heritage · Driven by Purpose</p>
            </div>

            <div class="p-7 sm:p-10">
                <a href="/" class="text-xs font-bold uppercase tracking-[0.18em] text-[#006B3C]">← Back to BBHS</a>
                <div class="mt-10">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Member Portal</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Member Login</h2>
                    <p class="mt-2 text-sm text-gray-500">Sign in to continue.</p>
                </div>

                <div v-if="page.props.flash?.success" class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>

                <form class="mt-7 space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Email Address</label>
                        <div class="relative">
                            <Mail :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-200 py-3 pl-10 pr-4 text-sm" placeholder="you@example.com" />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="mb-2 flex justify-between"><label class="text-sm font-semibold text-gray-700">Password</label><a href="#" class="text-xs font-bold text-[#006B3C]">Forgot password?</a></div>
                        <div class="relative">
                            <LockKeyhole :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="w-full rounded-lg border-gray-200 py-3 pl-10 pr-20 text-sm" placeholder="Your password" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500" @click="showPassword = !showPassword">{{ showPassword ? 'Hide' : 'Show' }}</button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600"><input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-[#006B3C]" /> Remember me</label>

                    <button :disabled="form.processing" type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3.5 text-sm font-extrabold text-white disabled:opacity-60">
                        {{ form.processing ? 'SIGNING IN...' : 'SIGN IN' }} <ArrowRight :size="16" />
                    </button>
                </form>

                <div class="mt-7 border-t border-gray-100 pt-6 text-center">
                    <p class="text-sm text-gray-500">Not a member yet?</p>
                    <a href="/register" class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">Join the Brotherhood <ArrowRight :size="15" /></a>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
