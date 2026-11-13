<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
defineProps({ application: { type: Object, required: true } });
</script>
<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[950px] px-5 py-10 lg:px-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Confidential welfare case</p><h1 class="mt-2 text-3xl font-extrabold text-white">{{ application.subject }}</h1><p class="mt-2 text-sm text-white/70">{{ application.reference }}</p></div></section>
<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-[950px] space-y-5 px-5 lg:px-8">
<div class="rounded-xl border bg-white p-6"><div class="flex flex-wrap justify-between gap-3"><span class="text-sm font-bold text-[#006B3C]">{{ application.category }}</span><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold capitalize">{{ application.status.replaceAll('_',' ') }}</span></div><p class="mt-5 whitespace-pre-line text-sm leading-7 text-gray-700">{{ application.description }}</p><p v-if="application.amount_requested" class="mt-4 text-sm font-bold">Amount requested: ₦{{ Number(application.amount_requested).toLocaleString() }}</p></div>
<div v-if="application.attachments?.length" class="rounded-xl border bg-white p-6"><h2 class="font-extrabold text-[#00351F]">Your supporting documents</h2><a v-for="file in application.attachments" :key="file.id" :href="`/welfare/${application.id}/attachments/${file.id}`" class="mt-3 block text-sm font-semibold text-[#006B3C] underline">{{ file.original_name }}</a></div>
<div class="rounded-xl border bg-white p-6"><h2 class="font-extrabold text-[#00351F]">Review update</h2><p class="mt-2 whitespace-pre-line text-sm text-gray-600">{{ application.admin_notes || 'No review update has been added yet.' }}</p><p v-if="application.amount_approved" class="mt-3 text-sm font-bold">Approved amount: ₦{{ Number(application.amount_approved).toLocaleString() }}</p></div>
<div v-if="application.disbursements?.length" class="rounded-xl border bg-white p-6"><h2 class="font-extrabold text-[#00351F]">Disbursement history</h2><div v-for="d in application.disbursements" :key="d.id" class="mt-3 border-t pt-3 text-sm"><b>₦{{ Number(d.amount).toLocaleString() }}</b> · {{ d.disbursed_on }} · {{ d.method || 'Method not specified' }}</div></div>
<Link href="/welfare" class="inline-block text-sm font-bold text-[#006B3C]">← Back to my applications</Link>
</div></section>
</PublicLayout>
</template>
