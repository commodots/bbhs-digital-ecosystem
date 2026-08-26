<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { CalendarDays, ArrowLeft } from 'lucide-vue-next';
const image = '/images/bbhs/events/reunion-01.jpg';
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F] py-14">
    <div class="mx-auto max-w-[1000px] px-5 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Association News</p>
        <h1 class="mt-3 text-4xl font-extrabold leading-tight text-white sm:text-5xl">BBHS Old Boys Continue a Legacy of Connection and Impact</h1>
        <div class="mt-5 flex items-center gap-2 text-sm text-white/60"><CalendarDays :size="15" /> May 15, 2026</div>
    </div>
</section>

<section class="bg-white py-12">
    <article class="mx-auto max-w-[1000px] px-5 lg:px-8">
        <img :src="image" alt="BBHS Old Boys" class="max-h-[520px] w-full rounded-2xl object-cover" />
        <div class="mx-auto mt-8 max-w-3xl">
            <p class="text-base leading-8 text-gray-600">Old Boys from different generations continue to come together to celebrate brotherhood, strengthen relationships and support the continued development of BBHS.</p>
            <p class="mt-5 text-base leading-8 text-gray-600">This page will later be powered by the Laravel news database, allowing administrators to publish stories, images, categories and publication dates without editing the Vue application.</p>
            <a href="/news" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"><ArrowLeft :size="16" /> Back to News</a>
        </div>
    </article>
</section>
</PublicLayout>
</template>
