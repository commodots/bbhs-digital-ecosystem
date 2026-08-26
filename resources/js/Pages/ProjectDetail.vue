<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowRight, CheckCircle2 } from 'lucide-vue-next';
const image = '/images/bbhs/school/bbhs-school-gate.jpg';
</script>

<template>
<PublicLayout>
<section class="relative min-h-[420px] overflow-hidden bg-[#00351F]">
    <img :src="image" alt="BBHS project" class="absolute inset-0 h-full w-full object-cover" />
    <div class="absolute inset-0 bg-[#00351F]/80"></div>
    <div class="relative mx-auto flex min-h-[420px] max-w-[1200px] items-end px-5 py-14 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Featured Project</p><h1 class="mt-3 text-4xl font-extrabold text-white sm:text-5xl">Supporting the Future of BBHS</h1></div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-[1050px] px-5 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
            <div>
                <h2 class="text-2xl font-extrabold text-[#00351F]">About the Project</h2>
                <p class="mt-4 text-sm leading-7 text-gray-600">This project supports initiatives that improve the learning environment and create better opportunities for present and future BBHS students.</p>
                <div class="mt-7 space-y-3">
                    <div v-for="item in ['Improve learning opportunities','Support students and teachers','Strengthen school facilities','Create lasting community impact']" :key="item" class="flex gap-3 text-sm text-gray-600"><CheckCircle2 :size="18" class="shrink-0 text-[#006B3C]" /> {{ item }}</div>
                </div>
            </div>
            <aside class="rounded-2xl bg-[#F7F9F7] p-6">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#006B3C]">Funding</p>
                <p class="mt-2 text-3xl font-extrabold text-[#00351F]">₦45M</p>
                <p class="text-xs text-gray-500">raised of ₦60M target</p>
                <div class="mt-5 h-2 rounded-full bg-gray-200"><div class="h-full w-3/4 rounded-full bg-[#006B3C]"></div></div>
                <button class="mt-6 w-full rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F]">SUPPORT PROJECT <ArrowRight :size="15" class="inline" /></button>
            </aside>
        </div>
    </div>
</section>
</PublicLayout>
</template>
