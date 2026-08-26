<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Search, Users, Eye, CheckCircle2, XCircle, PauseCircle } from 'lucide-vue-next';

const props = defineProps({
    members: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({ search: '', status: '' }) },
});

const page = usePage();

const filter = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

const search = () => filter.get('/admin/members', {
    preserveState: true,
    replace: true,
});

const action = (url) => {
    useForm({}).post(url, { preserveScroll: true });
};

const statusClass = (status) => ({
    approved: 'bg-green-50 text-green-700',
    pending: 'bg-amber-50 text-amber-700',
    suspended: 'bg-red-50 text-red-700',
    rejected: 'bg-gray-100 text-gray-600',
}[status] ?? 'bg-gray-100 text-gray-600');
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1400px] px-5 py-12 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Administration</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Members</h1>
        <p class="mt-2 text-sm text-white/70">Review registrations and manage alumni directory membership.</p>
    </div>
</section>

<section class="bg-[#F7F9F7] py-10">
<div class="mx-auto max-w-[1400px] px-5 lg:px-8">
    <div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>
    <div v-if="page.props.flash?.error" class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ page.props.flash.error }}</div>

    <form class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 md:grid-cols-[1fr_190px_auto]" @submit.prevent="search">
        <div class="relative">
            <Search :size="17" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input v-model="filter.search" class="w-full rounded-lg border-gray-200 py-3 pl-10 text-sm" placeholder="Search name or email..." />
        </div>
        <select v-model="filter.status" class="rounded-lg border-gray-200 px-3 py-3 text-sm">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="suspended">Suspended</option>
            <option value="rejected">Rejected</option>
        </select>
        <button class="rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white">FILTER</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div class="flex items-center gap-3 border-b border-gray-100 p-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EEF8F2] text-[#006B3C]"><Users :size="20" /></div>
            <div>
                <h2 class="font-extrabold text-[#00351F]">Member Applications</h2>
                <p class="text-xs text-gray-500">{{ members.total ?? members.data?.length ?? 0 }} records</p>
            </div>
        </div>

        <div v-if="!members.data?.length" class="p-10 text-center text-sm text-gray-500">No members found.</div>

        <div v-else class="divide-y divide-gray-100">
            <div v-for="member in members.data" :key="member.id" class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="font-extrabold text-[#00351F]">{{ member.name }}</h3>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase" :class="statusClass(member.member_status)">{{ member.member_status }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ member.email }}</p>
                    <p v-if="member.alumni_profile" class="mt-1 text-xs text-gray-400">
                        {{ member.alumni_profile.class_set || 'Class not provided' }}
                        <span v-if="member.alumni_profile.city"> · {{ member.alumni_profile.city }}</span>
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a :href="`/admin/members/${member.id}`" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700"><Eye :size="14" /> VIEW</a>
                    <button v-if="member.member_status === 'pending' || member.member_status === 'rejected'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-3 py-2 text-xs font-bold text-white" @click="action(`/admin/members/${member.id}/approve`)"><CheckCircle2 :size="14" /> APPROVE</button>
                    <button v-if="member.member_status === 'approved'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700" @click="action(`/admin/members/${member.id}/suspend`)"><PauseCircle :size="14" /> SUSPEND</button>
                    <button v-if="member.member_status === 'pending'" type="button" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-xs font-bold text-gray-700" @click="action(`/admin/members/${member.id}/reject`)"><XCircle :size="14" /> REJECT</button>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</PublicLayout>
</template>
