<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Save, UserCircle } from 'lucide-vue-next';

const page = usePage();

const profile = page.props.alumniProfile ?? {};

const form = useForm({
    first_name: profile.first_name ?? '',
    last_name: profile.last_name ?? '',
    phone: profile.phone ?? '',
    class_set: profile.class_set ?? '',
    profession: profile.profession ?? '',
    city: profile.city ?? '',
    country: profile.country ?? '',
    bio: profile.bio ?? '',
});

const submit = () => {
    form.put('/profile');
};
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Portal</p><h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">My Profile</h1></div></section>

<section class="bg-[#F7F9F7] py-12">
    <div class="mx-auto max-w-[1000px] px-5 lg:px-8">
        <form class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9" @submit.prevent="submit">
            <div class="flex items-center gap-4 border-b border-gray-100 pb-6">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#EEF8F2] text-[#006B3C]"><UserCircle :size="38" /></div>
                <div><h2 class="text-xl font-extrabold text-[#00351F]">{{ page.props.auth?.user?.name ?? 'Alumni Profile' }}</h2><p class="text-sm text-gray-500">Keep your information current.</p></div>
            </div>
            <div class="mt-7 grid gap-5 sm:grid-cols-2">
                <div><input v-model="form.first_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="First name" /><p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p></div>
                <div><input v-model="form.last_name" class="w-full rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Last name" /><p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p></div>
                <input v-model="form.phone" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Phone number" />
                <input v-model="form.class_set" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Class / Set" />
                <input v-model="form.profession" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Profession" />
                <input v-model="form.city" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="City" />
                <input v-model="form.country" class="rounded-lg border-gray-200 px-4 py-3 text-sm" placeholder="Country" />
                <textarea v-model="form.bio" class="min-h-[120px] rounded-lg border-gray-200 px-4 py-3 text-sm sm:col-span-2" placeholder="Short biography"></textarea>
            </div>
            <button :disabled="form.processing" type="submit" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-60"><Save :size="16" /> {{ form.processing ? 'SAVING...' : 'SAVE PROFILE' }}</button>
            <p v-if="form.recentlySuccessful" class="mt-3 text-sm text-green-700">Profile updated successfully.</p>
        </form>
    </div>
</section>
</PublicLayout>
</template>
