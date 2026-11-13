<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { MapPin, UsersRound, ArrowRight } from 'lucide-vue-next';

defineProps({
    chapters: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <PublicLayout>
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1200px] px-5 py-16 lg:px-8 lg:py-20">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#F1D36A]">Local connection • Shared heritage</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl">BBHS Chapters & Branches</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">
                    Stay connected with fellow Old Boys through geographical branches and local association activities.
                </p>
                <a href="/contact" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F] hover:bg-[#E5B93F]">
                    Enquire about a branch <ArrowRight :size="17" />
                </a>
            </div>
        </section>
        <section class="bg-white py-14 lg:py-16">
            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">Our network</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Find your BBHS community</h2>
                    <p class="mt-4 text-sm leading-7 text-gray-600">
                        Branches provide opportunities for members to connect locally, support Association initiatives and maintain lifelong relationships. The branches shown here are loaded directly from the BBHS database. Contact the Association to confirm current meeting arrangements and leadership.
                    </p>
                </div>
                <div v-if="chapters.length" class="mt-9 grid gap-5 sm:grid-cols-2">
                    <article v-for="chapter in chapters" :key="chapter.id" class="rounded-2xl border border-gray-200 p-6 transition hover:border-[#006B3C]/40 hover:shadow-sm">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                            <MapPin :size="23" />
                        </div>
                        <h3 class="mt-5 text-xl font-extrabold text-[#00351F]">{{ chapter.name }}</h3>
                        <p class="mt-1 text-xs font-semibold text-[#006B3C]">{{ chapter.location }}</p>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ chapter.note }}</p>
                    </article>
                </div>
                <div v-else class="mt-9 rounded-xl border border-dashed border-gray-300 p-8 text-sm text-gray-600">
                    Branch information is being updated. Please contact the Association for assistance.
                </div>
                <div class="mt-10 rounded-2xl bg-[#F3F8F4] p-7 sm:p-9">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="max-w-2xl">
                            <h2 class="text-2xl font-extrabold text-[#00351F]">Not seeing your location?</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Contact the Association to find out about an existing branch, update your location or express interest in supporting a chapter in your area.</p>
                        </div>
                        <a href="/contact" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white hover:bg-[#00552F]">
                            <UsersRound :size="17" /> Contact BBHS OBA
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
