<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ event: { type: Object, default: null } });

const form = useForm({
    title: props.event?.title ?? '',
    starts_at: props.event?.starts_at ?? '',
    ends_at: props.event?.ends_at ?? '',
    venue: props.event?.venue ?? '',
    location: props.event?.location ?? '',
    capacity: props.event?.capacity ?? '',
    excerpt: props.event?.excerpt ?? '',
    description: props.event?.description ?? '',
    image: props.event?.image ?? '',
    registration_open: props.event?.registration_open ?? false,
    published: props.event?.published ?? false,
});

const submit = () => {
    if (props.event) form.put(`/admin/events/${props.event.id}`);
    else form.post('/admin/events');
};
</script>

<template>
<PublicLayout>
<section class="bg-[#00351F]"><div class="mx-auto max-w-[1100px] px-5 py-12 lg:px-8"><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">Administration</p><h1 class="mt-2 text-3xl font-extrabold text-white">{{ event ? 'Edit Event' : 'New Event' }}</h1></div></section>

<section class="bg-[#F7F9F7] py-10"><div class="mx-auto max-w-[1100px] px-5 lg:px-8">
<form @submit.prevent="submit" class="rounded-2xl border bg-white p-7 sm:p-9">
<div class="grid gap-5 sm:grid-cols-2">
<input v-model="form.title" class="rounded-lg border-gray-200 px-4 py-3 sm:col-span-2" placeholder="Event title" />
<input v-model="form.event_date" type="date" class="rounded-lg border-gray-200 px-4 py-3" />
<input v-model="form.event_time" class="rounded-lg border-gray-200 px-4 py-3" placeholder="Time e.g. 10:00 AM" />
<input v-model="form.venue" class="rounded-lg border-gray-200 px-4 py-3 sm:col-span-2" placeholder="Venue" />
<input v-model="form.image" class="rounded-lg border-gray-200 px-4 py-3 sm:col-span-2" placeholder="/images/bbhs/events/event.jpg" />
<textarea v-model="form.excerpt" class="min-h-[90px] rounded-lg border-gray-200 px-4 py-3 sm:col-span-2" placeholder="Short excerpt"></textarea>
<textarea v-model="form.description" class="min-h-[260px] rounded-lg border-gray-200 px-4 py-3 sm:col-span-2" placeholder="Event description"></textarea>
<label class="flex items-center gap-3 text-sm font-semibold sm:col-span-2"><input v-model="form.published" type="checkbox" /> Publish this event</label>
</div>
<button :disabled="form.processing" class="mt-6 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white">{{ form.processing ? 'SAVING...' : 'SAVE EVENT' }}</button>
</form>
</div></section>
</PublicLayout>
</template>
