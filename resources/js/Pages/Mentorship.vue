<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { GraduationCap, UsersRound, Lightbulb, ArrowRight, Mail } from 'lucide-vue-next';

const benefits = [
    { icon: UsersRound, title: 'Meaningful connections', text: 'Build relationships across graduation sets and generations of the BBHS community.' },
    { icon: Lightbulb, title: 'Guidance and experience', text: 'Share practical knowledge, professional experience and lessons that can help others grow.' },
    { icon: GraduationCap, title: 'A stronger next generation', text: 'Encourage students and younger Old Boys through guidance, encouragement and access to experience.' },
];
</script>

<template>
    <PublicLayout>
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1200px] px-5 py-16 lg:px-8 lg:py-20">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#F1D36A]">Connect • Guide • Inspire</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl">BBHS Mentorship</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">
                    Bringing experience and opportunity together by connecting members of the BBHS community across generations.
                </p>
                <a href="/contact" class="mt-8 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F] hover:bg-[#E5B93F]">
                    Express your interest <ArrowRight :size="17" />
                </a>
            </div>
        </section>
        <section class="bg-white py-14 lg:py-16">
            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#006B3C]">The initiative</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">Experience shared. Potential strengthened.</h2>
                    <p class="mt-4 text-sm leading-7 text-gray-600">
                        The BBHS mentorship initiative is intended to create opportunities for experienced Old Boys to guide students and fellow alumni through knowledge-sharing, career insight and personal development. The Association is inviting interested mentors, prospective mentees and supporters to register their interest as the programme is developed.
                    </p>
                </div>
                <div class="mt-9 grid gap-5 md:grid-cols-3">
                    <article v-for="item in benefits" :key="item.title" class="rounded-2xl border border-gray-200 p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">
                            <component :is="item.icon" :size="23" />
                        </div>
                        <h3 class="mt-5 text-lg font-extrabold text-[#00351F]">{{ item.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ item.text }}</p>
                    </article>
                </div>
                <div class="mt-10 rounded-2xl bg-[#F3F8F4] p-7 sm:p-9">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="max-w-2xl">
                            <h2 class="text-2xl font-extrabold text-[#00351F]">Interested in participating?</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Contact the Association and indicate whether you would like to mentor, receive mentorship or support the initiative.</p>
                        </div>
                        <a href="/contact" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white hover:bg-[#00552F]">
                            <Mail :size="17" /> Contact BBHS OBA
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
