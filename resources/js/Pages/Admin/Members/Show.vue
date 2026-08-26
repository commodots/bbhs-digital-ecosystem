<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, PauseCircle, UserCircle } from 'lucide-vue-next';

const props = defineProps({ member: { type: Object, required: true } });
const page = usePage();

const action = (url) => useForm({}).post(url, { preserveScroll: true });
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1200px] px-5 py-12 lg:px-8">
        <a href="/admin/members" class="inline-flex items-center gap-2 text-xs font-bold text-white/70 hover:text-white"><ArrowLeft :size="14" /> BACK TO MEMBERS</a>
        <p class="mt-7 text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Member Details</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white">{{ member.name }}</h1>
    </div>
</section>

<section class="bg-[#F7F9F7] py-10">
<div class="mx-auto max-w-[1000px] px-5 lg:px-8">
    <div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>

    <div class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9">
        <div class="flex flex-col gap-5 border-b border-gray-100 pb-7 sm:flex-row sm:items-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-[#EEF8F2] text-[#006B3C]"><UserCircle :size="48" /></div>
            <div>
                <h2 class="text-2xl font-extrabold text-[#00351F]">{{ member.name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ member.email }}</p>
                <span class="mt-3 inline-block rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase text-amber-700">{{ member.member_status }}</span>
            </div>
        </div>

        <div v-if="member.alumni_profile" class="mt-7 grid gap-5 sm:grid-cols-2">
            <div><p class="text-xs font-bold uppercase text-gray-400">First Name</p><p class="mt-1 font-semibold">{{ member.alumni_profile.first_name || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Last Name</p><p class="mt-1 font-semibold">{{ member.alumni_profile.last_name || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Class / Set</p><p class="mt-1 font-semibold">{{ member.alumni_profile.class_set || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Profession</p><p class="mt-1 font-semibold">{{ member.alumni_profile.profession || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Phone</p><p class="mt-1 font-semibold">{{ member.alumni_profile.phone || '—' }}</p></div>
            <div><p class="text-xs font-bold uppercase text-gray-400">Location</p><p class="mt-1 font-semibold">{{ [member.alumni_profile.city, member.alumni_profile.country].filter(Boolean).join(', ') || '—' }}</p></div>
            <div class="sm:col-span-2"><p class="text-xs font-bold uppercase text-gray-400">Biography</p><p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">{{ member.alumni_profile.bio || 'No biography provided.' }}</p></div>
        </div>

        <div v-else class="mt-7 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">This member does not have an alumni profile yet.</div>

        <div class="mt-8 flex flex-wrap gap-3 border-t border-gray-100 pt-7">
            <button v-if="member.member_status !== 'approved'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white" @click="action(`/admin/members/${member.id}/approve`)"><CheckCircle2 :size="16" /> APPROVE MEMBER</button>
            <button v-if="member.member_status === 'approved'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-5 py-3 text-sm font-bold text-red-700" @click="action(`/admin/members/${member.id}/suspend`)"><PauseCircle :size="16" /> SUSPEND MEMBER</button>
        </div>
    </div>
</div>
</section>
</PublicLayout>
</template>
