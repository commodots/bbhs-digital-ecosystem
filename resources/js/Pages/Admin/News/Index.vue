<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Newspaper } from 'lucide-vue-next';

defineProps({ news: { type: Object, default: () => ({ data: [] }) } });
const page = usePage();

const destroy = (id) => {
    if (confirm('Delete this news article?')) {
        useForm({}).delete(`/admin/news/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1400px] px-5 py-12 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Administration</p>
        <div class="mt-2 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-white sm:text-4xl">News</h1>
                <p class="mt-2 text-sm text-white/70">Create and manage BBHS news.</p>
            </div>
            <a href="/admin/news/create" class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F]"><Plus :size="17" /> NEW ARTICLE</a>
        </div>
    </div>
</section>

<section class="bg-[#F7F9F7] py-10">
<div class="mx-auto max-w-[1400px] px-5 lg:px-8">
    <div v-if="page.props.flash?.success" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ page.props.flash.success }}</div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div v-if="!news.data?.length" class="p-10 text-center text-sm text-gray-500">No news articles yet.</div>
        <div v-else class="divide-y divide-gray-100">
            <div v-for="item in news.data" :key="item.id" class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EEF8F2] text-[#006B3C]"><Newspaper :size="19" /></div>
                        <div>
                            <h2 class="font-extrabold text-[#00351F]">{{ item.title }}</h2>
                            <p class="text-xs text-gray-500">{{ item.category || 'General' }} · {{ item.published ? 'Published' : 'Draft' }}</p>
                        </div>
                    </div>
                    <p class="mt-3 max-w-2xl text-sm text-gray-500">{{ item.excerpt }}</p>
                </div>
                <div class="flex gap-2">
                    <a :href="`/admin/news/${item.id}/edit`" class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-bold"><Pencil :size="14" /> EDIT</a>
                    <button @click="destroy(item.id)" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700"><Trash2 :size="14" /> DELETE</button>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</PublicLayout>
</template>
