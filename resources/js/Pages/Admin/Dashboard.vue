<script setup>
import PublicLayout from '../../Layouts/PublicLayout.vue';
import {
    Users,
    Newspaper,
    CalendarDays,
    FolderKanban,
    Images,
    FileText,
    MessageSquare,
    ArrowRight,
} from 'lucide-vue-next';

defineProps({
    admin: Object,
});

const cards = [
    {
        title: 'Members',
        description: 'Manage BBHS Old Boys membership and approvals.',
        icon: Users,
        href: '/admin/members',
    },
    {
        title: 'News',
        description: 'Create and manage news articles and updates.',
        icon: Newspaper,
        href: '/admin/news',
    },
    {
        title: 'Events',
        description: 'Manage reunions, meetings and upcoming events.',
        icon: CalendarDays,
        href: '/admin/events',
    },
    {
        title: 'Projects',
        description: 'Manage BBHS projects and support initiatives.',
        icon: FolderKanban,
        href: '/admin/projects',
    },
    {
        title: 'Gallery',
        description: 'Manage photographs and event albums.',
        icon: Images,
        href: '/admin/gallery',
    },
    {
        title: 'Resources',
        description: 'Manage documents, publications and resources.',
        icon: FileText,
        href: '/admin/resources',
    },
    {
        title: 'Messages',
        description: 'View enquiries and contact form submissions.',
        icon: MessageSquare,
        href: '/admin/messages',
    },
];
</script>

<template>
    <PublicLayout>

        <!-- Header -->
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1400px] px-5 py-12 lg:px-8">

                <p
                    class="text-xs font-bold uppercase tracking-[0.25em]"
                    style="color:#D4A72C;"
                >
                    Administration
                </p>

                <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">
                    Admin Dashboard
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/75">
                    Manage the BBHS Old Boys Association website,
                    members and community content.
                </p>

            </div>
        </section>


        <!-- Dashboard -->
        <section class="bg-[#F7F9F7] py-12 lg:py-16">

            <div class="mx-auto max-w-[1400px] px-5 lg:px-8">

                <!-- Welcome -->
                <div
                    class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <p class="text-sm text-gray-500">
                        Welcome back
                    </p>

                    <h2 class="mt-1 text-xl font-extrabold text-[#00351F]">
                        {{ admin?.name ?? 'Administrator' }}
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Select an area below to manage the BBHS digital ecosystem.
                    </p>
                </div>


                <!-- Management cards -->
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                    <a
                        v-for="card in cards"
                        :key="card.title"
                        :href="card.href"
                        class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#006B3C] hover:shadow-lg"
                    >

                        <div class="flex items-start justify-between">

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#EEF8F2] text-[#006B3C]"
                            >
                                <component
                                    :is="card.icon"
                                    :size="24"
                                />
                            </div>

                            <ArrowRight
                                :size="19"
                                class="text-gray-300 transition group-hover:translate-x-1 group-hover:text-[#006B3C]"
                            />

                        </div>

                        <h3
                            class="mt-5 text-lg font-extrabold text-[#00351F]"
                        >
                            {{ card.title }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            {{ card.description }}
                        </p>

                    </a>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>