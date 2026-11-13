<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ donations: { type: Array, default: () => [] }, pledges: { type: Array, default: () => [] } });
const money = value => '₦' + Number(value || 0).toLocaleString('en-NG', { maximumFractionDigits: 2 });
</script>
<template>
<PublicLayout>
    <Head title="My Fundraising Activity" />
    <section class="bg-[#00351F] text-white"><div class="mx-auto max-w-6xl px-5 py-10"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Member Portal</p><h1 class="mt-2 text-3xl font-extrabold">My Fundraising Activity</h1><p class="mt-2 text-sm text-white/70">Review your donations, pledges and campaign contributions.</p></div></section>
    <section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-6xl space-y-8 px-5">
        <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-xl font-extrabold text-[#00351F]">Donations</h2><a href="/fundraising" class="rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white">Browse campaigns</a></div>
        <div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-3">Campaign</th><th class="p-3">Amount</th><th class="p-3">Date</th><th class="p-3">Status</th><th class="p-3">Reference</th></tr></thead><tbody><tr v-for="item in donations" :key="item.id" class="border-t"><td class="p-3">{{item.campaign || 'Campaign'}}</td><td class="p-3 font-semibold">{{money(item.amount)}}</td><td class="p-3">{{item.paid_at || '—'}}</td><td class="p-3 capitalize">{{item.status}}</td><td class="p-3 text-xs">{{item.reference}}</td></tr><tr v-if="!donations.length"><td colspan="5" class="p-6 text-center text-gray-500">You have not made any donations yet.</td></tr></tbody></table></div>
        <h2 class="text-xl font-extrabold text-[#00351F]">Pledges</h2>
        <div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-3">Campaign</th><th class="p-3">Amount</th><th class="p-3">Date</th><th class="p-3">Status</th></tr></thead><tbody><tr v-for="item in pledges" :key="item.id" class="border-t"><td class="p-3">{{item.campaign || 'Campaign'}}</td><td class="p-3 font-semibold">{{money(item.amount)}}</td><td class="p-3">{{item.created_at}}</td><td class="p-3 capitalize">{{item.status}}</td></tr><tr v-if="!pledges.length"><td colspan="4" class="p-6 text-center text-gray-500">You have not made any pledges yet.</td></tr></tbody></table></div>
    </div></section>
</PublicLayout>
</template>
