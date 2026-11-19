<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { FileText, Music, ExternalLink } from 'lucide-vue-next';
defineProps({ documents: { type: Array, default: () => [] } });
</script>
<template>
<PublicLayout>
    <Head title="BBHS Resources" />
    <section class="relative overflow-hidden bg-[#00351F]"><div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8"><p class="text-xs font-bold uppercase tracking-widest text-[#D4A72C]">Knowledge · Heritage · Brotherhood</p><h1 class="mt-3 text-4xl font-extrabold text-white">BBHS Resources</h1><p class="mt-3 max-w-2xl text-sm leading-7 text-white/75">Official association documents, school history and heritage materials.</p></div></section>
    <section class="bg-[#F7F9F7] py-12"><div class="mx-auto max-w-[1100px] px-5 lg:px-8"><div class="grid gap-5 md:grid-cols-3"><article v-for="doc in documents" :key="doc.title" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"><component :is="doc.type === 'Audio' ? Music : FileText" class="text-[#006B3C]" :size="30"/><p class="mt-4 text-[10px] font-bold uppercase tracking-widest text-[#006B3C]">{{ doc.type }}</p><h2 class="mt-2 text-lg font-extrabold text-[#00351F]">{{ doc.title }}</h2><p class="mt-2 min-h-[52px] text-sm leading-6 text-gray-600">{{ doc.description }}</p><a :href="doc.url" :target="doc.type === 'Audio' ? '_blank' : '_self'" rel="noopener noreferrer" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white">{{ doc.type === 'Audio' ? 'Listen on Apple Music' : 'View / Download' }} <ExternalLink :size="14"/></a></article></div></div></section>
</PublicLayout>
</template>
