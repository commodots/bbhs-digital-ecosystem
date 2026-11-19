<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { UserRound, ArrowLeft, LockKeyhole } from 'lucide-vue-next';

defineProps({
    graduationSet: Object,
    members: { type: Array, default: () => [] },
});
</script>

<template>
<PublicLayout>
    <Head :title="`${graduationSet.name} Members`" />
    <section class="bg-[#00351F]">
        <div class="mx-auto max-w-[1100px] px-5 py-12 lg:px-8">
            <a href="/alumni" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white">
                <ArrowLeft :size="16" /> All graduation sets
            </a>
            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-[#D4A72C]">BBHS Alumni Network</p>
            <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">{{ graduationSet.name }}</h1>
            <p class="mt-2 text-sm text-white/75">{{ members.length }} publicly listed alumni</p>
            <p class="mt-2 max-w-2xl text-sm text-white/70">This page is publicly accessible. Members control which profile details are displayed.</p>
        </div>
    </section>

    <section class="bg-[#F7F9F7] py-10">
        <div class="mx-auto max-w-[1100px] px-5 lg:px-8">
            <div v-if="members.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="member in members" :key="member.id" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <a :href="`/alumni-directory/${member.id}`" class="group block text-center">
                        <div class="mx-auto flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-[#EEF8F2] text-[#006B3C]">
                            <img v-if="member.photo" :src="member.photo" :alt="member.name" class="h-full w-full object-cover" />
                            <UserRound v-else :size="42" />
                        </div>
                        <h2 class="mt-4 font-extrabold text-[#00351F] group-hover:text-[#087443]">{{ member.name }}</h2>
                        <p v-if="member.profession" class="mt-1 text-xs text-gray-500">{{ member.profession }}</p>
                        <p v-if="member.city || member.country" class="mt-1 text-xs text-gray-500">{{ [member.city, member.country].filter(Boolean).join(', ') }}</p>
                        <span class="mt-3 inline-block text-xs font-bold text-[#087443]">View public profile →</span>
                    </a>

                    <div v-if="member.phone || member.email" class="mt-4 border-t border-gray-100 pt-3 text-center">
                        <a v-if="member.phone" href="/login" class="mb-2 flex items-center justify-center gap-2 text-xs text-gray-600 hover:text-[#087443]">
                            <LockKeyhole :size="13" /> {{ member.phone }}
                        </a>
                        <a v-if="member.email" href="/login" class="flex items-center justify-center gap-2 text-xs text-gray-600 hover:text-[#087443]">
                            <LockKeyhole :size="13" /> {{ member.email }}
                        </a>
                        <p class="mt-2 text-[11px] text-gray-400">Sign in to access member contact options</p>
                    </div>
                </article>
            </div>
            <p v-else class="rounded-xl border border-dashed bg-white p-10 text-center text-sm text-gray-500">
                No approved members have opted to display their profiles in this set directory yet.
            </p>
        </div>
    </section>
</PublicLayout>
</template>
