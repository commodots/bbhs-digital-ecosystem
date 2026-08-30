<script setup>
import { useForm } from '@inertiajs/vue3';
import { ShieldCheck, LogIn, ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/admin/login');
};
</script>

<template>
    <div class="min-h-screen bg-[#F7F9F7]">

        <div class="bg-[#00351F]">
            <div class="mx-auto flex max-w-[1200px] items-center justify-between px-5 py-4 lg:px-8">
                <a href="/" class="text-sm font-bold text-white">
                    BBHS Old Boys Association
                </a>

                <a href="/" class="inline-flex items-center gap-2 text-xs font-bold text-white/80 hover:text-white">
                    <ArrowLeft :size="15" />
                    View Site
                </a>
            </div>
        </div>

        <main class="flex min-h-[calc(100vh-60px)] items-center justify-center px-5 py-12">
            <div class="w-full max-w-md">

                <div class="mb-7 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#EAF6EF] text-[#006B3C]">
                        <ShieldCheck :size="34" />
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                        Administration
                    </p>

                    <h1 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                        Admin Login
                    </h1>

                    <p class="mt-2 text-sm text-gray-500">
                        Sign in to manage the BBHS digital ecosystem.
                    </p>
                </div>

                <form
                    class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm sm:p-8"
                    @submit.prevent="submit"
                >
                    <div
                        v-if="form.errors.email"
                        class="mb-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        {{ form.errors.email }}
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Email address
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm focus:border-[#006B3C] focus:ring-[#006B3C]"
                            placeholder="admin@example.com"
                        />
                    </div>

                    <div class="mt-5">
                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Password
                        </label>

                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm focus:border-[#006B3C] focus:ring-[#006B3C]"
                            placeholder="Enter your password"
                        />
                    </div>

                    <label class="mt-4 flex items-center gap-2 text-sm text-gray-600">
                        <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-[#006B3C] focus:ring-[#006B3C]" />
                        Remember me
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3.5 text-sm font-bold text-white hover:bg-[#00552F] disabled:opacity-60"
                    >
                        <LogIn :size="17" />
                        {{ form.processing ? 'SIGNING IN...' : 'ADMIN LOGIN' }}
                    </button>

                    <div class="mt-5 text-center">
                        <a href="/login" class="text-sm font-semibold text-[#006B3C] hover:underline">
                            Member login instead
                        </a>
                    </div>
                </form>

            </div>
        </main>
    </div>
</template>
