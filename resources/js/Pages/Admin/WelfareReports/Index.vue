<script setup>
import MemberPortalLayout from '../../../Layouts/MemberPortalLayout.vue';

import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ filters: Object, summary: Object, statuses: Array, categories: Array, monthly: Array });
const from = ref(props.filters.from);
const to = ref(props.filters.to);
const money = value => '₦' + Number(value || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const count = value => Number(value || 0).toLocaleString();
function apply() {
    router.get('/admin/welfare-reports', { from: from.value, to: to.value }, { preserveState: true, replace: true });
}
function exportCsv() {
    const p = new URLSearchParams({ from: from.value, to: to.value });
    window.location.href = '/admin/welfare-reports/export?' + p.toString();
}
const cards = [
    ['applications', 'Applications received', 'count'],
    ['pending', 'Pending review', 'count'],
    ['approved', 'Approved in period', 'count'],
    ['requested', 'Amount requested', 'money'],
    ['disbursed_in_period', 'Disbursed in period', 'money'],
    ['outstanding_all_time', 'Outstanding approved balance', 'money'],
];
</script>

<template>
<MemberPortalLayout>
    <Head title="Welfare Reports" />
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1400px] px-5 py-8">
            <p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white">Welfare Reports</h1>
            <p class="mt-2 text-sm text-white/75">Confidential overview of welfare applications, approvals and assistance disbursements.</p>
        </div>
        <AdminQuickLinks />
    </section>
    <main class="mx-auto max-w-7xl px-5 py-8">
        <div class="flex flex-wrap items-end gap-3 rounded-xl border bg-white p-4">
            <div><label class="mb-1 block text-xs font-bold text-gray-500">From</label><input v-model="from" type="date" class="rounded-lg border-gray-300 text-sm"></div>
            <div><label class="mb-1 block text-xs font-bold text-gray-500">To</label><input v-model="to" type="date" class="rounded-lg border-gray-300 text-sm"></div>
            <button @click="apply" class="rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white">Apply filters</button>
            <button @click="exportCsv" class="ml-auto rounded-lg border px-4 py-2 text-sm font-bold text-gray-700">Export CSV</button>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div v-for="card in cards" :key="card[0]" class="rounded-xl border bg-white p-5">
                <p class="text-sm text-gray-500">{{ card[1] }}</p>
                <p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{ card[2] === 'money' ? money(summary[card[0]]) : count(summary[card[0]]) }}</p>
            </div>
        </div>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-xl border bg-white p-5">
                <h2 class="font-extrabold text-[#00351F]">Applications by status</h2>
                <div class="mt-3 divide-y">
                    <div v-for="item in statuses" :key="item.status" class="flex justify-between py-3 text-sm">
                        <span class="capitalize text-gray-700">{{ item.status.replaceAll('_', ' ') }}</span><strong>{{ count(item.total) }}</strong>
                    </div>
                </div>
            </section>
            <section class="rounded-xl border bg-white p-5">
                <h2 class="font-extrabold text-[#00351F]">Applications by category</h2>
                <div class="mt-3 divide-y">
                    <div v-for="item in categories" :key="item.category" class="flex justify-between py-3 text-sm">
                        <span class="text-gray-700">{{ item.category }}</span><strong>{{ count(item.total) }}</strong>
                    </div>
                    <p v-if="!categories.length" class="py-4 text-sm text-gray-500">No applications in this period.</p>
                </div>
            </section>
        </div>
        <section class="mt-6 rounded-xl border bg-white p-5">
            <h2 class="font-extrabold text-[#00351F]">Monthly activity</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead><tr class="border-b text-xs uppercase text-gray-500"><th class="py-3">Month</th><th class="py-3">Applications</th><th class="py-3">Disbursed</th></tr></thead>
                    <tbody><tr v-for="item in monthly" :key="item.month" class="border-b last:border-0"><td class="py-3">{{ item.month }}</td><td class="py-3">{{ count(item.applications) }}</td><td class="py-3">{{ money(item.disbursed) }}</td></tr></tbody>
                </table>
            </div>
            <p class="mt-3 text-xs text-gray-500">Application counts and requested amounts use submission dates. Disbursements use their actual disbursement dates. Outstanding approved balance is calculated across all dates.</p>
        </section>
    </main>
</MemberPortalLayout>
</template>
