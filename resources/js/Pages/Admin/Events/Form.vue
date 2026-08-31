<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    event: {
        type: Object,
        default: null,
    },
});

/*
|--------------------------------------------------------------------------
| Convert Laravel date/time into the format required by
| <input type="datetime-local">
|--------------------------------------------------------------------------
*/

const formatDateTimeLocal = (value) => {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const pad = (number) => String(number).padStart(2, '0');

    return (
        `${date.getFullYear()}-` +
        `${pad(date.getMonth() + 1)}-` +
        `${pad(date.getDate())}T` +
        `${pad(date.getHours())}:` +
        `${pad(date.getMinutes())}`
    );
};


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({

    title: props.event?.title ?? '',

    starts_at: formatDateTimeLocal(
        props.event?.starts_at
    ),

    ends_at: formatDateTimeLocal(
        props.event?.ends_at
    ),

    venue: props.event?.venue ?? '',

    location: props.event?.location ?? '',

    capacity: props.event?.capacity ?? '',

    excerpt: props.event?.excerpt ?? '',

    description: props.event?.description ?? '',

    image: props.event?.image ?? '',

    registration_open:
        Boolean(props.event?.registration_open),

    published:
        Boolean(props.event?.published),
});


/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {

    if (props.event) {

        form.put(
            `/admin/events/${props.event.id}`
        );

    } else {

        form.post('/admin/events');

    }
};
</script>


<template>

<PublicLayout>

    <!-- HEADER -->

    <section class="bg-[#00351F]">

        <div
            class="mx-auto max-w-[1100px] px-5 py-12 lg:px-8"
        >

            <p
                class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]"
            >
                Administration
            </p>

            <h1
                class="mt-2 text-3xl font-extrabold text-white sm:text-4xl"
            >
                {{ event ? 'Edit Event' : 'New Event' }}
            </h1>

        </div>

    </section>


    <!-- FORM -->

    <section class="bg-[#F7F9F7] py-10">

        <div
            class="mx-auto max-w-[1100px] px-5 lg:px-8"
        >

            <form
                @submit.prevent="submit"
                class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9"
            >

                <div class="grid gap-5 sm:grid-cols-2">


                    <!-- TITLE -->

                    <div class="sm:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Event Title
                        </label>

                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="Event title"
                        />

                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.title }}
                        </p>

                    </div>


                    <!-- START DATE/TIME -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Start Date & Time
                        </label>

                        <input
                            v-model="form.starts_at"
                            type="datetime-local"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                        />

                        <p
                            v-if="form.errors.starts_at"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.starts_at }}
                        </p>

                    </div>


                    <!-- END DATE/TIME -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            End Date & Time
                        </label>

                        <input
                            v-model="form.ends_at"
                            type="datetime-local"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                        />

                        <p
                            v-if="form.errors.ends_at"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.ends_at }}
                        </p>

                    </div>


                    <!-- VENUE -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Venue
                        </label>

                        <input
                            v-model="form.venue"
                            type="text"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="e.g. BBHS School Hall"
                        />

                    </div>


                    <!-- LOCATION -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Location
                        </label>

                        <input
                            v-model="form.location"
                            type="text"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="e.g. Abeokuta, Ogun State"
                        />

                    </div>


                    <!-- CAPACITY -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Capacity
                        </label>

                        <input
                            v-model="form.capacity"
                            type="number"
                            min="1"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="Optional"
                        />

                    </div>


                    <!-- IMAGE -->

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Event Image
                        </label>

                        <input
                            v-model="form.image"
                            type="text"
                            class="w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="/images/bbhs/events/event.jpg"
                        />

                    </div>


                    <!-- EXCERPT -->

                    <div class="sm:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Short Excerpt
                        </label>

                        <textarea
                            v-model="form.excerpt"
                            class="min-h-[90px] w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="Short description of the event"
                        ></textarea>

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="sm:col-span-2">

                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Event Description
                        </label>

                        <textarea
                            v-model="form.description"
                            class="min-h-[220px] w-full rounded-lg border-gray-200 px-4 py-3"
                            placeholder="Full event description"
                        ></textarea>

                    </div>


                    <!-- REGISTRATION -->

                    <label
                        class="flex items-center gap-3 text-sm font-semibold text-[#00351F]"
                    >

                        <input
                            v-model="form.registration_open"
                            type="checkbox"
                        />

                        Registration is open

                    </label>


                    <!-- PUBLISHED -->

                    <label
                        class="flex items-center gap-3 text-sm font-semibold text-[#00351F]"
                    >

                        <input
                            v-model="form.published"
                            type="checkbox"
                        />

                        Publish this event

                    </label>

                </div>


                <!-- SAVE -->

                <button
                    :disabled="form.processing"
                    type="submit"
                    class="mt-7 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:opacity-60"
                >

                    {{ form.processing ? 'SAVING...' : 'SAVE EVENT' }}

                </button>


                <!-- ERROR -->

                <p
                    v-if="form.errors && Object.keys(form.errors).length"
                    class="mt-4 text-sm text-red-600"
                >
                    Please check the highlighted fields and try again.
                </p>

            </form>

        </div>

    </section>

</PublicLayout>

</template>