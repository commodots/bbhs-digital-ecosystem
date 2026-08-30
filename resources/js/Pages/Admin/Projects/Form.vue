<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ArrowLeft, Upload, Save, Image as ImageIcon } from 'lucide-vue-next';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    title: props.project?.title ?? '',
    category: props.project?.category ?? '',
    excerpt: props.project?.excerpt ?? '',
    description: props.project?.description ?? '',
    image: null,
    target_amount: props.project?.target_amount ?? '',
    raised_amount: props.project?.raised_amount ?? '',
    status: props.project?.status ?? 'active',
    starts_on: props.project?.starts_on ?? '',
    ends_on: props.project?.ends_on ?? '',
    published: props.project?.published ?? false,
});

const imagePreview = props.project?.image
    ? (
        props.project.image.startsWith('http://') ||
        props.project.image.startsWith('https://') ||
        props.project.image.startsWith('/storage/')
            ? props.project.image
            : `/storage/${props.project.image}`
      )
    : null;

const handleImage = (event) => {
    const file = event.target.files?.[0] ?? null;
    form.image = file;
};

const submit = () => {
    if (props.project) {
        form
            .transform((data) => ({
                ...data,
                _method: 'PUT',
            }))
            .post(`/admin/projects/${props.project.id}`, {
                forceFormData: true,
                preserveScroll: true,
            });
    } else {
        form.post('/admin/projects', {
            forceFormData: true,
            preserveScroll: true,
        });
    }
};
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]">
    <div class="mx-auto max-w-[1100px] px-5 py-10 lg:px-8">
        <a
            href="/admin/projects"
            class="inline-flex items-center gap-2 text-xs font-bold text-white/70 hover:text-white"
        >
            <ArrowLeft :size="14" />
            BACK TO PROJECTS
        </a>

        <p class="mt-6 text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
            Administration
        </p>

        <h1 class="mt-2 text-3xl font-extrabold text-white">
            {{ project ? 'Edit Project' : 'New Project' }}
        </h1>
    </div>
</section>

<section class="bg-[#F7F9F7] py-10">
    <div class="mx-auto max-w-[1100px] px-5 lg:px-8">

        <form
            @submit.prevent="submit"
            class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm sm:p-9"
        >
            <div class="grid gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Project Title
                    </label>
                    <input
                        v-model="form.title"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="e.g. BBHS Science Laboratory Project"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">
                        {{ form.errors.title }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Category
                    </label>
                    <input
                        v-model="form.category"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="Infrastructure"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Project Status
                    </label>
                    <select
                        v-model="form.status"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                    >
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Target Amount (₦)
                    </label>
                    <input
                        v-model="form.target_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="25,000,000"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Total amount required to complete the project.
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Amount Raised (₦)
                    </label>
                    <input
                        v-model="form.raised_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="17,500,000"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Amount already received towards this project.
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Project Image
                    </label>

                    <div class="rounded-xl border-2 border-dashed border-gray-200 p-5">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                            <div
                                v-if="imagePreview"
                                class="h-28 w-40 overflow-hidden rounded-lg bg-gray-100"
                            >
                                <img
                                    :src="imagePreview"
                                    alt="Current project image"
                                    class="h-full w-full object-cover"
                                />
                            </div>

                            <div
                                v-else
                                class="flex h-28 w-40 items-center justify-center rounded-lg bg-[#EEF8F2] text-[#006B3C]"
                            >
                                <ImageIcon :size="34" />
                            </div>

                            <div class="flex-1">
                                <label
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white"
                                >
                                    <Upload :size="17" />
                                    CHOOSE IMAGE
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="handleImage"
                                    />
                                </label>

                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG or WEBP. Maximum 5MB.
                                </p>

                                <p
                                    v-if="form.image"
                                    class="mt-2 text-xs font-semibold text-[#006B3C]"
                                >
                                    {{ form.image.name }}
                                </p>

                                <p
                                    v-if="form.errors.image"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.image }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Start Date
                    </label>
                    <input
                        v-model="form.starts_on"
                        type="date"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        End Date
                    </label>
                    <input
                        v-model="form.ends_on"
                        type="date"
                        class="w-full rounded-lg border-gray-200 px-4 py-3"
                    />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Short Summary
                    </label>
                    <textarea
                        v-model="form.excerpt"
                        class="min-h-[90px] w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="Brief description shown on project cards."
                    ></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-[#00351F]">
                        Full Project Description
                    </label>
                    <textarea
                        v-model="form.description"
                        class="min-h-[260px] w-full rounded-lg border-gray-200 px-4 py-3"
                        placeholder="Describe the project, its purpose, impact and how members can support it."
                    ></textarea>
                </div>

                <label class="flex items-center gap-3 text-sm font-semibold sm:col-span-2">
                    <input
                        v-model="form.published"
                        type="checkbox"
                        class="rounded border-gray-300 text-[#006B3C]"
                    />
                    Publish this project on the public website
                </label>
            </div>

            <div class="mt-7 flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-60"
                >
                    <Save :size="17" />
                    {{ form.processing ? 'SAVING...' : 'SAVE PROJECT' }}
                </button>

                <a
                    href="/admin/projects"
                    class="inline-flex items-center rounded-lg border border-gray-200 px-6 py-3 text-sm font-bold text-gray-700"
                >
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</section>
</PublicLayout>
</template>
