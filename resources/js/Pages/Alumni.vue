<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowRight, Users, GraduationCap, MapPin, Search, ShieldCheck } from 'lucide-vue-next';

defineProps({
    stats: { type: Object, default: () => ({ members: 0, sets: 0, branches: 0 }) },
    graduationSets: { type: Array, default: () => [] },
    executiveCount: { type: Number, default: 0 },
    spotlights: { type: Array, default: () => [] },
});
</script>

<template>
    <PublicLayout>
        <section class="relative overflow-hidden bg-[#00351F]">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 80% 20%, #D4A72C 0, transparent 28%), radial-gradient(circle at 10% 90%, #087A3A 0, transparent 35%);"></div>
            <div class="relative mx-auto max-w-[1200px] px-5 py-16 lg:px-8 lg:py-20">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#F1D36A]">One School · Many Generations · One Brotherhood</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl">BBHS Alumni Network</h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-white/80">A living network connecting Old Boys across graduation sets, cities and generations. Reconnect with classmates, keep your profile current and take part in the life of the Association.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="/membership" class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3 text-sm font-extrabold text-[#00351F] hover:bg-[#E5B93F]">Join the Brotherhood <ArrowRight :size="16" /></a>
                    <a href="/alumni-directory" class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-6 py-3 text-sm font-bold text-white hover:bg-white/10"><Search :size="16" /> Member Directory</a>
                </div>
            </div>
        </section>

        <section class="bg-white py-10 sm:py-14">
            <div class="mx-auto grid max-w-[1100px] gap-4 px-5 sm:grid-cols-3 lg:px-8">
                <div class="rounded-2xl border border-gray-100 bg-[#F7F9F7] p-6"><Users class="text-[#087A3A]" :size="25" /><p class="mt-4 text-3xl font-extrabold text-[#00351F]">{{ Number(stats.members || 0).toLocaleString() }}</p><p class="mt-1 text-sm text-gray-500">Approved alumni profiles</p></div>
                <div class="rounded-2xl border border-gray-100 bg-[#F7F9F7] p-6"><GraduationCap class="text-[#087A3A]" :size="25" /><p class="mt-4 text-3xl font-extrabold text-[#00351F]">{{ Number(stats.sets || 0).toLocaleString() }}</p><p class="mt-1 text-sm text-gray-500">Graduation sets represented</p></div>
                <div class="rounded-2xl border border-gray-100 bg-[#F7F9F7] p-6"><MapPin class="text-[#087A3A]" :size="25" /><p class="mt-4 text-3xl font-extrabold text-[#00351F]">{{ Number(stats.branches || 0).toLocaleString() }}</p><p class="mt-1 text-sm text-gray-500">Active geographical branches</p></div>
            </div>
            <p class="mx-auto mt-4 max-w-[1100px] px-5 text-xs text-gray-400 lg:px-8">Figures are live counts from approved platform records and update as membership records are verified.</p>
        </section>

        <section v-if="spotlights.length" class="bg-white py-10 sm:py-14">
            <div class="mx-auto max-w-[1100px] px-5 lg:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#087A3A]">Celebrating our members</p>
                <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Alumni Spotlight</h2>
                <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <article v-for="person in spotlights" :key="person.id" class="overflow-hidden rounded-2xl border border-gray-100 bg-[#F7F9F7] shadow-sm">
                        <img v-if="person.photo" :src="person.photo" :alt="person.name" class="h-52 w-full object-cover object-top" />
                        <div class="p-5"><h3 class="font-extrabold text-[#00351F]">{{ person.name }}</h3><p v-if="person.class_set" class="mt-1 text-xs text-gray-500">{{ person.class_set }}</p><p v-if="person.headline" class="mt-3 text-sm font-bold text-[#087A3A]">{{ person.headline }}</p><p class="mt-2 text-sm leading-6 text-gray-600">{{ person.writeup }}</p><a :href="`/alumni-directory/${person.profile_id}`" class="mt-4 inline-flex text-sm font-bold text-[#087A3A]">View profile <ArrowRight :size="15" class="ml-2" /></a></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-[#F7F9F7] py-12 sm:py-16">
            <div class="mx-auto max-w-[1100px] px-5 lg:px-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div><p class="text-xs font-bold uppercase tracking-[0.2em] text-[#087A3A]">Our graduating community</p><h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Explore Graduation Sets</h2><p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600">The Association recognises each graduating year as a distinct set within the wider BBHS brotherhood.</p></div>
                    <a href="/alumni-directory" class="inline-flex items-center gap-2 text-sm font-bold text-[#087A3A] hover:text-[#00351F]">Browse member directory <ArrowRight :size="16" /></a>
                </div>
                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="set in graduationSets" :key="set.id" class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-4">
                        <div><p class="font-extrabold text-[#00351F]">{{ set.name }}</p><p class="mt-1 text-xs text-gray-500">Class of {{ set.graduation_year }}</p></div>
                        <span class="rounded-full bg-[#EEF8F2] px-3 py-1 text-xs font-bold text-[#087A3A]">{{ set.approved_members_count }} members</span>
                    </div>
                </div>
                <div v-if="!graduationSets.length" class="mt-8 rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">Graduation sets will appear here as the Association’s set records are configured.</div>
            </div>
        </section>

        <section class="bg-white py-12 sm:py-16">
            <div class="mx-auto grid max-w-[1100px] gap-8 px-5 md:grid-cols-2 lg:px-8">
                <div class="rounded-2xl bg-[#00351F] p-7 text-white sm:p-9"><ShieldCheck class="text-[#F1D36A]" :size="30" /><h2 class="mt-5 text-2xl font-extrabold">A trusted alumni directory</h2><p class="mt-3 text-sm leading-7 text-white/75">The member directory is reserved for approved members. Personal phone numbers and email addresses are not displayed publicly. Members can hide their entire profile from the directory. Contact details remain masked for directory viewers.</p><a href="/alumni-directory" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F]">Open Directory <ArrowRight :size="16" /></a></div>
                <div class="rounded-2xl border border-gray-200 bg-[#F7F9F7] p-7 sm:p-9"><p class="text-xs font-bold uppercase tracking-[0.2em] text-[#087A3A]">Get involved</p><h2 class="mt-3 text-2xl font-extrabold text-[#00351F]">Keep your set connected</h2><p class="mt-3 text-sm leading-7 text-gray-600">Update your member profile, take part in reunions and Association events, support school projects and contribute to welfare and fundraising initiatives.</p><div class="mt-6 flex flex-wrap gap-3"><a href="/membership" class="rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white">Become a Member</a><a href="/events" class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700">Upcoming Events</a></div></div>
            </div>
        </section>
    </PublicLayout>
</template>
