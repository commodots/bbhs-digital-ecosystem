<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
const props = defineProps({ application: { type: Object, required: true } });
const page = usePage();
const review = useForm({ status: props.application.status, amount_approved: props.application.amount_approved || '', admin_notes: props.application.admin_notes || '' });
const payout = useForm({ amount: '', disbursed_on: new Date().toISOString().slice(0,10), method: '', reference: '', notes: '' });
const saveReview = () => review.patch(`/admin/welfare/${props.application.id}`, { preserveScroll: true });
const recordPayout = () => payout.post(`/admin/welfare/${props.application.id}/disbursements`, { preserveScroll: true, onSuccess: () => payout.reset('amount','reference','notes') });
</script>
<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-9 lg:px-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Confidential welfare case</p><h1 class="mt-2 text-3xl font-extrabold text-white">{{ application.subject }}</h1><p class="mt-2 text-sm text-white/70">{{ application.reference }} · {{ application.user?.name }} · {{ application.user?.email }}</p></div><AdminQuickLinks/></section>
<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-[1200px] space-y-6 px-5 lg:px-8">
<div v-if="page.props.flash?.success" class="rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
<div class="grid gap-6 lg:grid-cols-2">
<div class="space-y-5">
<div class="rounded-xl border bg-white p-6"><div class="flex justify-between gap-3"><b class="text-[#006B3C]">{{ application.category }}</b><span class="text-xs font-bold capitalize">{{ application.status.replaceAll('_',' ') }}</span></div><p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">{{ application.description }}</p><p class="mt-4 text-sm">Requested: <b>₦{{ Number(application.amount_requested||0).toLocaleString() }}</b></p><p class="mt-1 text-sm">Approved: <b>₦{{ Number(application.amount_approved||0).toLocaleString() }}</b></p></div>
<div class="rounded-xl border bg-white p-6"><h2 class="font-extrabold text-[#00351F]">Supporting documents</h2><p v-if="!application.attachments?.length" class="mt-2 text-sm text-gray-500">No attachments.</p><a v-for="file in application.attachments" :key="file.id" :href="`/admin/welfare/${application.id}/attachments/${file.id}`" class="mt-3 block text-sm font-semibold text-[#006B3C] underline">{{ file.original_name }}</a></div>
<div class="rounded-xl border bg-white p-6"><h2 class="font-extrabold text-[#00351F]">Disbursement history</h2><div v-for="item in application.disbursements" :key="item.id" class="mt-3 border-t pt-3 text-sm"><b>₦{{ Number(item.amount).toLocaleString() }}</b> · {{ item.disbursed_on }} · {{ item.method || 'Not specified' }}<p class="text-xs text-gray-500">{{ item.reference }}</p></div><p v-if="!application.disbursements?.length" class="mt-2 text-sm text-gray-500">No disbursements recorded.</p></div>
</div>
<div class="space-y-5">
<form @submit.prevent="saveReview" class="space-y-4 rounded-xl border bg-white p-6"><h2 class="text-xl font-extrabold text-[#00351F]">Review application</h2>
<label class="block text-sm font-bold">Decision / status<select v-model="review.status" class="mt-1 w-full rounded-lg border-gray-300"><option value="under_review">Under review</option><option value="more_information">More information required</option><option value="approved">Approved</option><option value="rejected">Rejected</option><option value="closed">Closed</option></select></label>
<label class="block text-sm font-bold">Approved amount (₦)<input v-model="review.amount_approved" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border-gray-300"/></label>
<label class="block text-sm font-bold">Private committee notes / member update<textarea v-model="review.admin_notes" rows="5" class="mt-1 w-full rounded-lg border-gray-300"></textarea></label>
<button :disabled="review.processing" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Save review</button></form>
<form v-if="application.status==='approved'" @submit.prevent="recordPayout" class="space-y-4 rounded-xl border bg-white p-6"><h2 class="text-xl font-extrabold text-[#00351F]">Record disbursement</h2>
<label class="block text-sm font-bold">Amount (₦)<input v-model="payout.amount" required type="number" min="0.01" step="0.01" class="mt-1 w-full rounded-lg border-gray-300"/></label>
<label class="block text-sm font-bold">Disbursement date<input v-model="payout.disbursed_on" required type="date" class="mt-1 w-full rounded-lg border-gray-300"/></label>
<label class="block text-sm font-bold">Method<input v-model="payout.method" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Bank transfer, cheque, etc."/></label>
<label class="block text-sm font-bold">Reference<input v-model="payout.reference" class="mt-1 w-full rounded-lg border-gray-300"/></label>
<label class="block text-sm font-bold">Notes<textarea v-model="payout.notes" rows="2" class="mt-1 w-full rounded-lg border-gray-300"/></label>
<button :disabled="payout.processing" class="rounded-lg bg-[#00351F] px-5 py-3 text-sm font-bold text-white">Record disbursement</button></form>
<Link href="/admin/welfare" class="inline-block text-sm font-bold text-[#006B3C]">← Back to welfare applications</Link>
</div></div></div></section>
</PublicLayout>
</template>
