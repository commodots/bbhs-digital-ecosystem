<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowLeft, CalendarDays } from 'lucide-vue-next';

defineProps({ news: { type: Object, required: true } });
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F] py-14">
<div class="mx-auto max-w-[1000px] px-5 lg:px-8">
<p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">{{ news.category || 'Association News' }}</p>
<h1 class="mt-3 text-4xl font-extrabold leading-tight text-white sm:text-5xl">{{ news.title }}</h1>
<div class="mt-5 flex items-center gap-2 text-sm text-white/60"><CalendarDays :size="15" /> {{ news.published_at || 'Recently published' }}</div>
</div>
</section>
<section class="bg-white py-12">
<article class="mx-auto max-w-[1000px] px-5 lg:px-8">
<img v-if="news.image" :src="news.image" :alt="news.title" class="max-h-[520px] w-full rounded-2xl object-cover" />
<div class="mx-auto mt-8 max-w-3xl">
<p v-if="news.excerpt" class="text-lg font-medium leading-8 text-gray-600">{{ news.excerpt }}</p>
<div class="mt-7 whitespace-pre-line text-base leading-8 text-gray-600">{{ news.body }}</div>
<a href="/news" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"><ArrowLeft :size="16" /> Back to News</a>
</div>
</article>
</section>
</PublicLayout>
</template>
