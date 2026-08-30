<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowRight, CalendarDays, Newspaper } from 'lucide-vue-next';

defineProps({ news: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
<div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8">
<p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Association News</p>
<h1 class="mt-3 text-4xl font-extrabold text-white sm:text-5xl">News & Media</h1>
<p class="mt-4 max-w-2xl text-sm leading-7 text-white/70">Updates, stories and announcements from the BBHS Old Boys Association.</p>
</div>
</section>
<section class="bg-[#F7F9F7] py-14 lg:py-16">
<div class="mx-auto max-w-[1200px] px-5 lg:px-8">
<div v-if="!news.data?.length" class="rounded-2xl border bg-white p-12 text-center text-gray-500">No published news yet.</div>
<div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
<article v-for="item in news.data" :key="item.id" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
<div class="h-52 overflow-hidden bg-[#00351F]">
<img v-if="item.image" :src="item.image" :alt="item.title" class="h-full w-full object-cover" />
<div v-else class="flex h-full items-center justify-center text-white/70"><Newspaper :size="42" /></div>
</div>
<div class="p-6">
<div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[#006B3C]"><CalendarDays :size="14" /> {{ item.published_at || 'Recent' }}</div>
<p class="mt-3 text-xs font-bold uppercase tracking-wide text-[#D4A72C]">{{ item.category || 'Association News' }}</p>
<h2 class="mt-2 text-xl font-extrabold leading-snug text-[#00351F]">{{ item.title }}</h2>
<p class="mt-3 text-sm leading-6 text-gray-500">{{ item.excerpt }}</p>
<a :href="`/news/${item.slug}`" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">Read Story <ArrowRight :size="16" /></a>
</div>
</article>
</div>
</div>
</section>
</PublicLayout>
</template>
