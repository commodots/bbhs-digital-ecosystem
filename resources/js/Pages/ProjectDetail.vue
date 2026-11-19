<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from 'lucide-vue-next';

defineProps({
    project: { type: Object, required: true },
});
const money = (value) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(Number(value || 0));
</script>

<template>
<PublicLayout>
<section class="relative min-h-[420px] overflow-hidden bg-[#00351F]">
    <img :src="project.image" :alt="project.title" class="absolute inset-0 h-full w-full object-cover" />
    <div class="absolute inset-0 bg-[#00351F]/80"></div>
    <div class="relative mx-auto flex min-h-[420px] max-w-[1200px] items-end px-5 py-14 lg:px-8">
        <div>
            <Link href="/projects" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"><ArrowLeft :size="16"/> Back to Projects</Link>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">{{ project.category || 'BBHS Project' }}</p>
            <h1 class="mt-3 text-4xl font-extrabold text-white sm:text-5xl">{{ project.title }}</h1>
            <p v-if="project.excerpt" class="mt-4 max-w-3xl text-white/80">{{ project.excerpt }}</p>
        </div>
    </div>
</section>
<section class="bg-white py-12">
    <div class="mx-auto max-w-[1050px] px-5 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
            <div>
                <h2 class="text-2xl font-extrabold text-[#00351F]">About the Project</h2>
                <div class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-600">{{ project.description || project.excerpt || 'Project information will be updated soon.' }}</div>
                <div class="mt-7 space-y-3">
                    <div v-for="item in ['Support BBHS students and the school','Strengthen facilities and opportunities','Create lasting community impact']" :key="item" class="flex gap-3 text-sm text-gray-600"><CheckCircle2 :size="18" class="shrink-0 text-[#006B3C]" /> {{ item }}</div>
                </div>
                <div v-if="project.campaigns?.length" class="mt-8 border-t border-gray-100 pt-6"><h3 class="text-xl font-extrabold text-[#00351F]">Support this project</h3><div class="mt-4 space-y-3"><div v-for="campaign in project.campaigns" :key="campaign.id" class="rounded-xl border border-gray-200 p-4"><h4 class="font-bold text-[#00351F]">{{ campaign.title }}</h4><p class="mt-1 text-xs text-gray-500">₦{{ Number(campaign.raised_amount).toLocaleString('en-NG') }} raised of ₦{{ Number(campaign.target_amount).toLocaleString('en-NG') }}</p><div class="mt-2 h-2 rounded-full bg-gray-100"><div class="h-full rounded-full bg-[#006B3C]" :style="{width: campaign.progress + '%'}"></div></div><Link :href="`/fundraising/${campaign.slug}`" class="mt-3 inline-flex rounded-lg bg-[#006B3C] px-4 py-2 text-sm font-bold text-white">Donate to this campaign</Link></div></div></div>
                <p v-if="project.starts_on || project.ends_on" class="mt-7 text-xs text-gray-500">Project period: {{ project.starts_on || 'To be announced' }} – {{ project.ends_on || 'Ongoing' }}</p>
            </div>
            <aside class="rounded-2xl bg-[#F7F9F7] p-6">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#006B3C]">Funding</p>
                <p class="mt-2 text-3xl font-extrabold text-[#00351F]">{{ money(project.raised_amount) }}</p>
                <p class="text-xs text-gray-500">raised of {{ money(project.target_amount) }} target</p>
                <div class="mt-5 h-2 overflow-hidden rounded-full bg-gray-200"><div class="h-full rounded-full bg-[#006B3C]" :style="{ width: `${project.progress || 0}%` }"></div></div>
                <p class="mt-2 text-right text-xs font-semibold text-[#006B3C]">{{ project.progress || 0 }}% funded</p>
                <div v-if="project.campaigns?.length" class="mt-5 space-y-3">
                    <p class="text-xs leading-5 text-gray-600">Choose an active fundraising campaign to contribute to this project. Members can donate directly using the existing secure Paystack checkout.</p>
                    <Link v-for="campaign in project.campaigns" :key="campaign.id" :href="`/fundraising/${campaign.slug}`" class="flex w-full items-center justify-center rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-extrabold text-[#00351F] hover:bg-[#e6c75e]">DONATE TO {{ campaign.title }}</Link>
                </div>
                <div v-else class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-bold text-amber-900">Donations not yet open</p>
                    <p class="mt-1 text-xs leading-5 text-amber-800">There is no active fundraising campaign linked to this project yet. The Association will publish a donation campaign when contributions are open.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
</PublicLayout>
</template>
