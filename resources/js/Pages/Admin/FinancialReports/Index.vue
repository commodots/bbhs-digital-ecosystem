<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
const props=defineProps({filters:Object,totals:Object,duesStatus:Object,pending:Object,monthly:Array,campaigns:Array});
const from=ref(props.filters.from), to=ref(props.filters.to);
const money=v=>'₦'+Number(v||0).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2});
function apply(){router.get('/admin/financial-reports',{from:from.value,to:to.value},{preserveState:true,replace:true});}
function exportCsv(){window.location.href='/admin/financial-reports/export?'+new URLSearchParams({from:from.value,to:to.value}).toString();}
</script>
<template>
<PublicLayout><Head title="Financial Reports"/>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1400px] px-5 py-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">Financial Reports & Reconciliation</h1><p class="mt-2 text-sm text-white/75">Consolidated view of membership dues, fundraising income, pledges and welfare disbursements.</p></div><AdminQuickLinks/></section>
<div class="mx-auto max-w-7xl px-5 py-8">
<div class="flex flex-wrap items-end justify-between gap-3 rounded-xl border bg-white p-4">
<div><label class="mb-1 block text-xs font-bold text-gray-500">From</label><input v-model="from" type="date" class="rounded-lg border-gray-300"/></div>
<div><label class="mb-1 block text-xs font-bold text-gray-500">To</label><input v-model="to" type="date" class="rounded-lg border-gray-300"/></div>
<div class="flex gap-2"><button @click="apply" class="rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white">Apply filters</button><button @click="exportCsv" class="rounded-lg border px-4 py-2 text-sm font-bold">Export CSV</button></div>
</div>
<div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
<div v-for="item in [{label:'Membership dues collected',value:money(totals.dues_collected)},{label:'Donations received',value:money(totals.donations_received)},{label:'Total income',value:money(totals.total_income)},{label:'Welfare disbursed',value:money(totals.welfare_disbursed)}]" class="rounded-xl border bg-white p-5"><p class="text-sm text-gray-500">{{item.label}}</p><p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{item.value}}</p></div>
</div>
<div class="mt-4 grid gap-4 lg:grid-cols-2">
<div class="rounded-xl border bg-white p-5"><h2 class="font-extrabold text-[#00351F]">Membership dues position</h2><div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4"><div><p class="text-xs text-gray-500">Accounts</p><b class="text-xl">{{duesStatus.total}}</b></div><div><p class="text-xs text-gray-500">Up to date</p><b class="text-xl text-green-700">{{duesStatus.up_to_date}}</b></div><div><p class="text-xs text-gray-500">Outstanding</p><b class="text-xl text-amber-700">{{duesStatus.outstanding}}</b></div><div><p class="text-xs text-gray-500">Exempt</p><b class="text-xl">{{duesStatus.exempt}}</b></div></div></div>
<div class="rounded-xl border bg-white p-5"><h2 class="font-extrabold text-[#00351F]">Items requiring attention</h2><div class="mt-4 grid grid-cols-3 gap-3"><div><p class="text-xs text-gray-500">Pending dues</p><b class="text-xl">{{pending.dues}}</b></div><div><p class="text-xs text-gray-500">Pending donations</p><b class="text-xl">{{pending.donations}}</b></div><div><p class="text-xs text-gray-500">Open pledges</p><b class="text-xl">{{pending.pledges_open}}</b></div></div></div>
</div>
<div class="mt-6 rounded-xl border bg-white p-5"><h2 class="font-extrabold text-[#00351F]">Monthly income and welfare outflow</h2><div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50 text-gray-500"><tr><th class="p-3">Month</th><th class="p-3">Dues</th><th class="p-3">Donations</th><th class="p-3">Welfare disbursed</th></tr></thead><tbody><tr v-for="m in monthly" :key="m.month" class="border-t"><td class="p-3 font-semibold">{{m.month}}</td><td class="p-3">{{money(m.dues)}}</td><td class="p-3">{{money(m.donations)}}</td><td class="p-3">{{money(m.welfare)}}</td></tr><tr v-if="!monthly.length"><td colspan="4" class="p-4 text-gray-500">No activity in this date range.</td></tr></tbody></table></div><p class="mt-3 text-xs text-gray-500">Income includes successful dues payments and donations. Welfare is shown as an outflow. Pledges are commitments, not cash received.</p></div>
<div class="mt-6 rounded-xl border bg-white p-5"><h2 class="font-extrabold text-[#00351F]">Fundraising campaign performance</h2><div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50 text-gray-500"><tr><th class="p-3">Campaign</th><th class="p-3">Scope</th><th class="p-3">Status</th><th class="p-3">Target</th><th class="p-3">Raised</th><th class="p-3">Donations</th></tr></thead><tbody><tr v-for="c in campaigns" :key="c.id" class="border-t"><td class="p-3 font-semibold">{{c.title}}</td><td class="p-3 capitalize">{{c.scope_type}}</td><td class="p-3 capitalize">{{c.status}}</td><td class="p-3">{{money(c.target)}}</td><td class="p-3 font-bold text-green-700">{{money(c.raised)}}</td><td class="p-3">{{c.donations}}</td></tr><tr v-if="!campaigns.length"><td colspan="6" class="p-4 text-gray-500">No campaigns recorded.</td></tr></tbody></table></div></div>
<div class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900"><b>Accounting note:</b> This report is an operational summary, not a full audited ledger. It currently reports verified dues, verified donations and recorded welfare disbursements. Pledges are reported separately as commitments; pending transactions are not counted as income.</div>
</div></PublicLayout>
</template>
