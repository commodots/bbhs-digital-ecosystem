<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ArrowRight, LockKeyhole, UserPlus } from 'lucide-vue-next';

const showPassword = ref(false);
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    class_set: '',
    city: '',
    country: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/register');
</script>

<template>
<div class="min-h-screen bg-[#F7F9F7]">
    <div class="mx-auto flex min-h-screen max-w-[1100px] items-center justify-center px-5 py-10">
        <div class="w-full max-w-3xl rounded-2xl border border-gray-200 bg-white p-7 shadow-xl sm:p-10">
            <a href="/" class="text-xs font-bold uppercase tracking-[0.18em] text-[#006B3C]">← Back to BBHS</a>
            <div class="mt-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Member Registration</p>
                <h1 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">Join the Brotherhood</h1>
                <p class="mt-2 text-sm text-gray-500">Create your BBHS Old Boys Association member account.</p>
            </div>

            <form class="mt-8 grid gap-5 sm:grid-cols-2" @submit.prevent="submit">
                <div>
                    <input v-model="form.first_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="First name" />
                    <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p>
                </div>
                <div>
                    <input v-model="form.last_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Last name" />
                    <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p>
                </div>
                <div>
                    <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Email address" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>
                <input v-model="form.phone" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Phone number" />
                <input v-model="form.class_set" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="BBHS class / set" />
                <input v-model="form.city" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Current city" />
                <input v-model="form.country" class="rounded-lg border-gray-200 px-4 py-3 text-sm sm:col-span-2" placeholder="Country" />

                <div class="relative sm:col-span-2">
                    <LockKeyhole :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="w-full rounded-lg border-gray-200 py-3 pl-10 pr-16 text-sm" placeholder="Create password" />
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500" @click="showPassword = !showPassword">{{ showPassword ? 'Hide' : 'Show' }}</button>
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <input v-model="form.password_confirmation" type="password" class="rounded-lg border-gray-200 px-4 py-3 text-sm sm:col-span-2" placeholder="Confirm password" />

                <label class="flex gap-2 text-sm text-gray-600 sm:col-span-2">
                    <input type="checkbox" required class="mt-1 rounded border-gray-300 text-[#006B3C]" />
                    I agree to the BBHS Old Boys Association membership terms and privacy policy.
                </label>

                <button :disabled="form.processing" type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3.5 text-sm font-extrabold text-white disabled:opacity-60 sm:col-span-2">
                    <UserPlus :size="16" /> {{ form.processing ? 'CREATING ACCOUNT...' : 'CREATE MEMBER ACCOUNT' }} <ArrowRight :size="16" />
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already registered?
                <a href="/login" class="font-bold text-[#006B3C]">Sign in</a>
            </p>
        </div>
    </div>
</div>
</template>
