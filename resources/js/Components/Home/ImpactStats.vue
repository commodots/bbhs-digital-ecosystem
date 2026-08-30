<script setup>
import { UsersRound, Globe2, CalendarDays, Newspaper, HandHeart } from 'lucide-vue-next';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
});

const stats = [
    { key: 'alumni', label: 'Approved Alumni', icon: UsersRound, format: value => `${Number(value || 0).toLocaleString()}+` },
    { key: 'countries', label: 'Countries Represented', icon: Globe2, format: value => `${Number(value || 0).toLocaleString()}+` },
    { key: 'events', label: 'Published Events', icon: CalendarDays, format: value => `${Number(value || 0).toLocaleString()}+` },
    { key: 'news', label: 'News Stories', icon: Newspaper, format: value => `${Number(value || 0).toLocaleString()}+` },
    { key: 'raised', label: 'Funds Raised (₦)', icon: HandHeart, format: value => `₦${value || '0'}` },
];
</script>

<template>
    <section class="px-5 py-3 lg:px-8 lg:py-4">
        <div class="mx-auto max-w-[1440px]">
            <div class="overflow-hidden rounded-2xl bg-[#00351F] shadow-sm">
                <div class="grid lg:grid-cols-[1.2fr_repeat(5,1fr)]">
                    <div class="flex items-center border-b border-white/15 px-6 py-5 lg:border-b-0 lg:border-r lg:px-7">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#F1D36A]">Our Impact</p>
                            <h2 class="mt-1 text-lg font-extrabold leading-tight text-white">Building a legacy <span class="block">that lasts.</span></h2>
                        </div>
                    </div>
                    <div v-for="stat in stats" :key="stat.label" class="flex items-center gap-3 border-b border-white/15 px-5 py-4 lg:border-b-0 lg:border-r last:border-r-0">
                        <component :is="stat.icon" :size="25" :stroke-width="1.7" class="shrink-0 text-[#D4A72C]" />
                        <div>
                            <div class="text-xl font-extrabold leading-none text-white">{{ stat.format(props.stats[stat.key]) }}</div>
                            <div class="mt-1 text-[9px] font-medium uppercase leading-4 tracking-wide text-white/65">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
