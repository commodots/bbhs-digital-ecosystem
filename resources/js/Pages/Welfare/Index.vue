<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { HeartHandshake, Plus, FileText } from 'lucide-vue-next';
defineProps({ applications: { type: Object, default: () => ({ data: [] }) } });
</script>
<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1100px] px-5 py-12 lg:px-8">
<p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Member services</p><h1 class="mt-3 text-4xl font-extrabold text-white">Welfare Support</h1>
<p class="mt-3 max-w-2xl text-sm leading-7 text-white/70">Submit and privately track requests for support from the Association.</p>
<Link href="/welfare/create" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F]"><Plus :size="17"/> New welfare application</Link>
</div></section>
<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-[1100px] space-y-4 px-5 lg:px-8">
<div v-if="!applications.data?.length" class="rounded-xl border bg-white p-10 text-center text-gray-500"><HeartHandshake :size="36" class="mx-auto mb-3 text-[#006B3C]"/>You have not submitted any welfare applications.</div>
<div v-for="item in applications.data" :key="item.id" class="flex flex-wrap items-center justify-between gap-4 rounded-xl border bg-white p-5">
<div><p class="text-xs font-bold text-[#006B3C]">{{ item.reference }} · {{ item.category }}</p><h2 class="mt-1 font-extrabold text-[#00351F]">{{ item.subject }}</h2><p class="mt-1 text-xs text-gray-500">Submitted {{ new Date(item.created_at).toLocaleDateString() }}</p></div>
<div class="flex items-center gap-3"><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold capitalize">{{ item.status.replaceAll('_',' ') }}</span><Link :href="`/welfare/${item.id}`" class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-bold text-[#006B3C]"><FileText :size="15"/> View</Link></div>
</div>
</div></section>
</PublicLayout>
</template>
