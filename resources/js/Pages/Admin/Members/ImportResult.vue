<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { ArrowLeft, CheckCircle2, AlertCircle, Mail } from 'lucide-vue-next';
defineProps({
 summary: { type: Object, default: () => ({}) },
 results: { type: Array, default: () => [] },
});
</script>
<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-6xl px-5 py-9"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Membership Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">Import completed</h1></div><AdminQuickLinks /></section>
<section class="bg-[#F7F9F7] py-9"><div class="mx-auto max-w-6xl space-y-6 px-5">
 <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <div class="rounded-xl bg-white p-5"><p class="text-xs text-gray-500">Members created</p><p class="mt-2 text-3xl font-extrabold text-green-700">{{ summary.created }}</p></div>
  <div class="rounded-xl bg-white p-5"><p class="text-xs text-gray-500">Skipped / failed</p><p class="mt-2 text-3xl font-extrabold text-red-700">{{ summary.skipped }}</p></div>
  <div class="rounded-xl bg-white p-5"><p class="text-xs text-gray-500">Welcome emails sent</p><p class="mt-2 text-3xl font-extrabold text-green-700">{{ summary.email_sent }}</p></div>
  <div class="rounded-xl bg-white p-5"><p class="text-xs text-gray-500">Email failures</p><p class="mt-2 text-3xl font-extrabold text-amber-700">{{ summary.email_failed }}</p></div>
 </div>
 <div class="overflow-hidden rounded-xl border bg-white"><div class="border-b p-5 font-extrabold">Import details</div><div class="divide-y"><div v-for="(row, i) in results" :key="i" class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold">{{ row.email }} <span class="text-xs text-gray-400">· CSV line {{ row.line }}</span></p><p class="text-sm text-gray-500">{{ row.message }}</p></div><span class="text-xs font-bold" :class="row.status.startsWith('Imported') ? 'text-green-700' : 'text-red-700'">{{ row.status }}</span></div></div></div>
 <div class="rounded-xl bg-blue-50 p-4 text-sm text-blue-900"><Mail class="mr-2 inline" :size="16"/> Members whose email could not be sent can receive a new password setup link from their member record.</div>
 <a href="/admin/members" class="inline-flex items-center gap-2 font-bold text-[#006B3C]"><ArrowLeft :size="15"/> Return to members</a>
</div></section>
</PublicLayout>
</template>
