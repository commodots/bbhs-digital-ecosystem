<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

import {
    ArrowRight,
    BookOpen,
    FileText,
    Image,
    HelpCircle,
    Download,
    ExternalLink,
} from 'lucide-vue-next';

const resources = [
    {
        icon: FileText,
        title: 'BBHS Constitution',
        description:
            'Access the constitution and governing framework of the BBHS Old Boys Association.',
        action: 'View Document',
        type: 'Document',
    },
    {
        icon: BookOpen,
        title: 'Alumni Resources',
        description:
            'Useful information and resources for BBHS Old Boys across classes and locations.',
        action: 'Explore',
        type: 'Resource',
    },
    {
        icon: Image,
        title: 'BBHS Gallery',
        description:
            'Browse photographs and memories from school activities, reunions and alumni gatherings.',
        action: 'View Gallery',
        type: 'Media',
    },
    {
        icon: HelpCircle,
        title: 'Frequently Asked Questions',
        description:
            'Find answers to common questions about membership, events, projects and the association.',
        action: 'View FAQs',
        type: 'Help',
    },
];

const documents = [
    {
        title: 'BBHS Old Boys Association Constitution',
        description: 'Association constitution and governance information.',
        type: 'PDF',
        size: 'Available soon',
    },
    {
        title: 'Membership Information',
        description: 'Information for existing and prospective members.',
        type: 'Document',
        size: 'Available soon',
    },
    {
        title: 'BBHS Association Information Pack',
        description: 'An overview of the association, its purpose and activities.',
        type: 'PDF',
        size: 'Available soon',
    },
];
</script>

<template>
    <PublicLayout>

        <!-- HERO -->
        <section class="relative overflow-hidden bg-[#00351F]">

            <div class="absolute inset-0 bg-[#00351F]"></div>

            <div class="relative mx-auto flex min-h-[350px] max-w-[1440px] items-center px-5 py-16 lg:px-10">

                <div class="max-w-3xl">

                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]">
                        BBHS Resources
                    </p>

                    <h1 class="mt-4 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Knowledge.
                        <span class="block text-[#16A34A]">
                            Connection.
                        </span>
                        <span class="block">
                            Resources.
                        </span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">
                        Access useful information, documents, media and
                        resources for BBHS Old Boys and the wider community.
                    </p>

                </div>

            </div>

        </section>


        <!-- RESOURCE CARDS -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-8">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                        Resource Centre
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl">
                        Everything You Need
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-500">
                        Find information, documents and useful resources
                        relating to the BBHS Old Boys Association.
                    </p>

                </div>


                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <article
                        v-for="resource in resources"
                        :key="resource.title"
                        class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:border-[#006B3C]/30 hover:shadow-md"
                    >

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">

                            <component
                                :is="resource.icon"
                                :size="22"
                            />

                        </div>

                        <p class="mt-5 text-[9px] font-bold uppercase tracking-[0.16em] text-[#006B3C]">
                            {{ resource.type }}
                        </p>

                        <h3 class="mt-2 text-lg font-extrabold text-[#00351F]">
                            {{ resource.title }}
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-gray-500">
                            {{ resource.description }}
                        </p>

                        <a
                            href="#"
                            class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                        >
                            {{ resource.action }}
                            <ArrowRight :size="15" />
                        </a>

                    </article>

                </div>

            </div>

        </section>


        <!-- DOCUMENTS -->
        <section class="bg-[#F7F9F7] py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="mb-7">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]">
                        Documents
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-[#00351F]">
                        Important Documents
                    </h2>

                </div>


                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">

                    <div
                        v-for="(document, index) in documents"
                        :key="document.title"
                        class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between"
                        :class="{
                            'border-b border-gray-200': index !== documents.length - 1
                        }"
                    >

                        <div class="flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]">

                                <FileText :size="20" />

                            </div>

                            <div>

                                <h3 class="font-bold text-[#00351F]">
                                    {{ document.title }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ document.description }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center gap-4 sm:shrink-0">

                            <div class="text-right">

                                <p class="text-xs font-bold text-[#006B3C]">
                                    {{ document.type }}
                                </p>

                                <p class="text-[10px] text-gray-400">
                                    {{ document.size }}
                                </p>

                            </div>

                            <button
                                type="button"
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-[#006B3C] transition hover:bg-[#EEF8F2]"
                            >
                                <Download :size="17" />
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- QUICK LINKS -->
        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="grid gap-5 md:grid-cols-3">

                    <a
                        href="/news"
                        class="group rounded-2xl border border-gray-200 p-6 transition hover:border-[#006B3C]/30 hover:shadow-md"
                    >

                        <h3 class="font-extrabold text-[#00351F]">
                            News & Media
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Stay up to date with BBHS news, stories and
                            announcements.
                        </p>

                        <span class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">
                            Explore
                            <ArrowRight :size="15" />
                        </span>

                    </a>


                    <a
                        href="/events"
                        class="group rounded-2xl border border-gray-200 p-6 transition hover:border-[#006B3C]/30 hover:shadow-md"
                    >

                        <h3 class="font-extrabold text-[#00351F]">
                            Events & Reunions
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Discover upcoming events, reunions and
                            association activities.
                        </p>

                        <span class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">
                            View Events
                            <ArrowRight :size="15" />
                        </span>

                    </a>


                    <a
                        href="/gallery"
                        class="group rounded-2xl border border-gray-200 p-6 transition hover:border-[#006B3C]/30 hover:shadow-md"
                    >

                        <h3 class="font-extrabold text-[#00351F]">
                            Photo Gallery
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            Relive memories from BBHS events, reunions and
                            special occasions.
                        </p>

                        <span class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]">
                            View Gallery
                            <ExternalLink :size="14" />
                        </span>

                    </a>

                </div>

            </div>

        </section>


        <!-- CTA -->
        <section class="bg-[#F7F9F7] py-12">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="rounded-2xl bg-[#00351F] px-7 py-10 text-center sm:px-10">

                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                        BBHS Old Boys Association
                    </p>

                    <h2 class="mt-3 text-3xl font-extrabold text-white">
                        Need More Information?
                    </h2>

                    <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-white/70">
                        If you cannot find what you are looking for, get in
                        touch with the association.
                    </p>

                    <a
                        href="/contact"
                        class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3 text-sm font-extrabold text-[#00351F]"
                    >
                        CONTACT US
                        <ArrowRight :size="16" />
                    </a>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>