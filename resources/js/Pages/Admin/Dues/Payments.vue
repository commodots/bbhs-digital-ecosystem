<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
const props=defineProps({payments:Object,filters:Object,summary:Object});
const form=reactive({status:props.filters.status||'',method:props.filters.method||'',search:props.filters.search||''});
const apply=()=>router.get('/admin/dues/payments',form,{preserveState:true,replace:true});
const money=v=>'₦'+Number(v||0).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2});
</script>
<template><PublicLayout><Head title="Membership Payments"/>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1400px] px-5 py-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">Membership Payments</h1><p class="mt-2 text-sm text-white/70">Review successful, pending and failed dues transactions.</p></div><AdminQuickLinks/></section>
<section class="mx-auto max-w-[1400px] space-y-6 px-5 py-8">
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-gray-500">Collected</p><p class="mt-2 text-2xl font-extrabold text-[#00351F]">{{money(summary.collected)}}</p></div>
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-gray-500">Successful</p><p class="mt-2 text-2xl font-extrabold">{{summary.successful}}</p></div>
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-gray-500">Pending</p><p class="mt-2 text-2xl font-extrabold">{{summary.pending}}</p></div>
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-gray-500">Failed</p><p class="mt-2 text-2xl font-extrabold">{{summary.failed}}</p></div>
</div>
<div class="rounded-xl border bg-white p-5">
<div class="grid gap-3 md:grid-cols-4"><input v-model="form.search" @keyup.enter="apply" placeholder="Search member or reference" class="rounded-lg border-gray-300"/><select v-model="form.status" class="rounded-lg border-gray-300"><option value="">All statuses</option><option value="success">Successful</option><option value="pending">Pending</option><option value="failed">Failed</option></select><select v-model="form.method" class="rounded-lg border-gray-300"><option value="">All methods</option><option value="paystack">Paystack</option><option value="bank_transfer">Bank transfer</option></select><button @click="apply" class="rounded-lg bg-[#006B3C] px-4 py-2 font-bold text-white">Filter</button></div>
<div class="mt-5 overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b text-gray-500"><th class="py-3">Member</th><th>Reference</th><th>Plan</th><th>Method</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody><tr v-for="p in payments.data" :key="p.id" class="border-b"><td class="py-3"><a :href="`/admin/dues/${p.user_id}`" class="font-bold text-[#006B3C]">{{p.member}}</a><p class="text-xs text-gray-500">{{p.email}}</p></td><td class="font-mono text-xs">{{p.reference}}</td><td class="capitalize">{{p.period_type}}</td><td class="capitalize">{{p.method.replace('_',' ')}}</td><td>{{money(p.amount)}}</td><td class="capitalize">{{p.status}}</td><td>{{p.paid_at||p.created_at}}</td></tr></tbody></table><p v-if="!payments.data.length" class="py-8 text-center text-gray-500">No payments match these filters.</p></div>
<div class="mt-4 flex justify-between text-sm"><span>Page {{payments.current_page}} of {{payments.last_page}}</span><div class="flex gap-2"><a v-if="payments.prev_page_url" :href="payments.prev_page_url" class="font-bold text-[#006B3C]">Previous</a><a v-if="payments.next_page_url" :href="payments.next_page_url" class="font-bold text-[#006B3C]">Next</a></div></div>
</div></section></PublicLayout></template>
