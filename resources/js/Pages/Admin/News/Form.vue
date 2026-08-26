<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ news: { type: Object, default: null } });

const form = useForm({
    title: props.news?.title ?? '',
    excerpt: props.news?.excerpt ?? '',
    body: props.news?.body ?? '',
    image: props.news?.image ?? '',
    category: props.news?.category ?? '',
    published: props.news?.published ?? false,
});

const submit = () => {
    if (props.news) {
        form.put(`/admin/news/${props.news.id}`);
    } else {
        form.post('/admin/news');
    }
};
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1100px] px-5 py-12 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">{{ news ? 'Edit News' : 'New Article' }}</h1></div></section>

<section class="bg-[#F7F9F7] py-10">
<div class="mx-auto max-w-[1100px] px-5 lg:px-8">
<form @submit.prevent="submit" class="rounded-2xl border bg-white p-7 sm:p-9">
<div class="grid gap-5">
<input v-model="form.title" class="rounded-lg border-gray-200 px-4 py-3" placeholder="Article title" />
<input v-model="form.category" class="rounded-lg border-gray-200 px-4 py-3" placeholder="Category" />
<input v-model="form.image" class="rounded-lg border-gray-200 px-4 py-3" placeholder="/images/bbhs/news/example.jpg" />
<textarea v-model="form.excerpt" class="min-h-[90px] rounded-lg border-gray-200 px-4 py-3" placeholder="Short excerpt"></textarea>
<textarea v-model="form.body" class="min-h-[280px] rounded-lg border-gray-200 px-4 py-3" placeholder="Full article content"></textarea>
<label class="flex items-center gap-3 text-sm font-semibold"><input v-model="form.published" type="checkbox" /> Publish this article</label>
</div>
<button :disabled="form.processing" class="mt-6 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white">{{ form.processing ? 'SAVING...' : 'SAVE ARTICLE' }}</button>
</form>
</div>
</section>
</PublicLayout>
</template>
