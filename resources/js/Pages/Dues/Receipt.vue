<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ payment: { type: Object, required: true } });
const money = value => '₦' + Number(value || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const printReceipt = () => window.print();
</script>
<template>
<PublicLayout><Head title="Membership Payment Receipt"/>
<section class="bg-[#F7F9F7] py-10 print:bg-white print:py-0"><div class="mx-auto max-w-3xl px-5 print:px-0">
<div class="mb-4 flex justify-end gap-3 print:hidden"><a href="/dues" class="rounded-lg border border-[#006B3C] px-4 py-2 text-sm font-bold text-[#006B3C]">Back to dues</a><button @click="printReceipt" class="rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white">Print / Save as PDF</button></div>
<article class="rounded-2xl border bg-white p-8 shadow-sm print:rounded-none print:border-0 print:shadow-none">
<div class="flex items-start justify-between gap-4 border-b pb-6"><div><p class="text-xs font-bold uppercase tracking-[.2em] text-[#006B3C]">Baptist Boys' High School</p><h1 class="mt-1 text-2xl font-extrabold text-[#00351F]">Old Boys' Association</h1><p class="mt-2 text-sm text-gray-500">Membership Dues Payment Receipt</p></div><span class="rounded-full bg-green-50 px-3 py-1 text-xs font-bold uppercase text-green-700">Payment confirmed</span></div>
<div class="grid gap-6 py-7 sm:grid-cols-2"><div><p class="text-xs text-gray-500">Received from</p><p class="mt-1 font-bold">{{payment.member_name}}</p><p class="text-sm text-gray-600">{{payment.member_email}}</p></div><div><p class="text-xs text-gray-500">Payment date</p><p class="mt-1 font-bold">{{payment.paid_at}}</p></div><div><p class="text-xs text-gray-500">Transaction reference</p><p class="mt-1 break-all font-mono text-sm font-bold">{{payment.reference}}</p></div><div><p class="text-xs text-gray-500">Method</p><p class="mt-1 font-bold">Paystack / Online payment</p></div><div><p class="text-xs text-gray-500">Membership plan</p><p class="mt-1 font-bold capitalize">{{payment.period_type}}</p></div><div><p class="text-xs text-gray-500">Period covered</p><p class="mt-1 font-bold">{{payment.period_start}} – {{payment.period_end}}</p></div></div>
<div class="rounded-xl bg-[#F2F8F4] p-5"><p class="text-sm text-gray-600">Amount received</p><p class="mt-1 text-3xl font-extrabold text-[#00351F]">{{money(payment.amount)}}</p></div>
<p class="mt-8 text-center text-xs text-gray-500">System-generated receipt for a verified BBHS Old Boys' Association membership dues payment.</p>
</article></div></section></PublicLayout>
</template>
