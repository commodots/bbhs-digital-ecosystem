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
| Convert existing Laravel date/time to datetime-local format
|--------------------------------------------------------------------------
*/

const formatDateTimeLocal = (value) => {
    if (!value) {
        return '';
    }

    // Laravel may return:
    // 2026-09-06T20:00:00.000000Z
    // or
    // 2026-09-06 20:00:00

    const normalized = String(value).replace(' ', 'T');

    const date = new Date(normalized);

    if (Number.isNaN(date.getTime())) {
        console.warn('Could not parse event date:', value);
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

    registration_open: Boolean(
        props.event?.registration_open
    ),

    published: Boolean(
        props.event?.published
    ),
});


/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
|
| This is called by:
|
| <form @submit.prevent="submit">
|
|--------------------------------------------------------------------------
*/

const submit = () => {

    console.log(
        '================================================'
    );

    console.log(
        'BBHS EVENT FORM SUBMISSION'
    );

    console.log(
        '================================================'
    );

    console.log(
        'Event ID:',
        props.event?.id ?? 'NEW EVENT'
    );

    console.log(
        'Title:',
        form.title
    );

    console.log(
        'STARTS_AT:',
        form.starts_at
    );

    console.log(
        'ENDS_AT:',
        form.ends_at
    );

    console.log(
        'Venue:',
        form.venue
    );

    console.log(
        'Location:',
        form.location
    );

    console.log(
        'Full form data:',
        { ...form.data() }
    );

    console.log(
        '================================================'
    );


    /*
    |--------------------------------------------------------------------------
    | Update existing event
    |--------------------------------------------------------------------------
    */

    if (props.event) {

        form.put(
            `/admin/events/${props.event.id}`,
            {
                preserveScroll: true,

                onSuccess: () => {

                    console.log(
                        'EVENT UPDATE SUCCESSFUL'
                    );

                },

                onError: (errors) => {

                    console.error(
                        'EVENT UPDATE VALIDATION ERRORS:',
                        errors
                    );

                },

            }
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Create new event
    |--------------------------------------------------------------------------
    */

    form.post(
        '/admin/events',
        {
            preserveScroll: true,

            onSuccess: () => {

                console.log(
                    'EVENT CREATED SUCCESSFULLY'
                );

            },

            onError: (errors) => {

                console.error(
                    'EVENT CREATION VALIDATION ERRORS:',
                    errors
                );

            },

        }
    );
};
</script>


<template>

<PublicLayout>

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

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

            <p class="mt-2 text-sm text-white/70">
                {{ event
                    ? 'Update the event information below.'
                    : 'Create a new BBHS event.'
                }}
            </p>

        </div>

    </section>


    <!-- =========================================================
         FORM SECTION
    ========================================================== -->

    <section class="bg-[#F7F9F7] py-10">

        <div
            class="mx-auto max-w-[1100px] px-5 lg:px-8"
        >

            <form
                @submit.prevent="submit"
                class="rounded-2xl border border-gray-200 bg-white p-7 sm:p-9"
            >

                <div class="grid gap-6 sm:grid-cols-2">


                    <!-- =================================================
                         TITLE
                    ================================================== -->

                    <div class="sm:col-span-2">

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Event Title
                        </label>

                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="e.g. Career Mentorship Summit"
                        />

                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.title }}
                        </p>

                    </div>


                    <!-- =================================================
                         START DATE / TIME
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Start Date & Time
                        </label>

                        <input
                            v-model="form.starts_at"
                            type="datetime-local"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                        />

                        <p
                            class="mt-1 text-xs text-gray-400"
                        >
                            Select when the event starts.
                        </p>

                        <p
                            v-if="form.errors.starts_at"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.starts_at }}
                        </p>

                    </div>


                    <!-- =================================================
                         END DATE / TIME
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            End Date & Time
                        </label>

                        <input
                            v-model="form.ends_at"
                            type="datetime-local"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                        />

                        <p
                            class="mt-1 text-xs text-gray-400"
                        >
                            Optional.
                        </p>

                        <p
                            v-if="form.errors.ends_at"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.ends_at }}
                        </p>

                    </div>


                    <!-- =================================================
                         VENUE
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Venue
                        </label>

                        <input
                            v-model="form.venue"
                            type="text"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="e.g. BBHS School Hall"
                        />

                        <p
                            v-if="form.errors.venue"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.venue }}
                        </p>

                    </div>


                    <!-- =================================================
                         LOCATION
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Location
                        </label>

                        <input
                            v-model="form.location"
                            type="text"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="e.g. Abeokuta, Ogun State"
                        />

                        <p
                            v-if="form.errors.location"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.location }}
                        </p>

                    </div>


                    <!-- =================================================
                         CAPACITY
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Capacity
                        </label>

                        <input
                            v-model="form.capacity"
                            type="number"
                            min="1"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="e.g. 100"
                        />

                        <p
                            v-if="form.errors.capacity"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.capacity }}
                        </p>

                    </div>


                    <!-- =================================================
                         IMAGE
                    ================================================== -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Event Image
                        </label>

                        <input
                            v-model="form.image"
                            type="text"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="/images/bbhs/events/event.jpg"
                        />

                        <p
                            class="mt-1 text-xs text-gray-400"
                        >
                            Image path for the event.
                        </p>

                        <p
                            v-if="form.errors.image"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.image }}
                        </p>

                    </div>


                    <!-- =================================================
                         EXCERPT
                    ================================================== -->

                    <div class="sm:col-span-2">

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Short Excerpt
                        </label>

                        <textarea
                            v-model="form.excerpt"
                            class="min-h-[90px] w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="Short description of the event"
                        ></textarea>

                        <p
                            v-if="form.errors.excerpt"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.excerpt }}
                        </p>

                    </div>


                    <!-- =================================================
                         DESCRIPTION
                    ================================================== -->

                    <div class="sm:col-span-2">

                        <label
                            class="mb-2 block text-sm font-semibold text-[#00351F]"
                        >
                            Event Description
                        </label>

                        <textarea
                            v-model="form.description"
                            class="min-h-[220px] w-full rounded-lg border border-gray-200 px-4 py-3"
                            placeholder="Full event description"
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>

                    </div>


                    <!-- =================================================
                         OPTIONS
                    ================================================== -->

                    <div class="sm:col-span-2 flex flex-wrap gap-8">

                        <label
                            class="flex items-center gap-3 text-sm font-semibold text-[#00351F]"
                        >

                            <input
                                v-model="form.registration_open"
                                type="checkbox"
                                class="rounded"
                            />

                            Registration is open

                        </label>


                        <label
                            class="flex items-center gap-3 text-sm font-semibold text-[#00351F]"
                        >

                            <input
                                v-model="form.published"
                                type="checkbox"
                                class="rounded"
                            />

                            Publish this event

                        </label>

                    </div>

                </div>


                <!-- =====================================================
                     FORM ERROR SUMMARY
                ====================================================== -->

                <div
                    v-if="Object.keys(form.errors).length"
                    class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-700"
                >

                    Please check the highlighted fields and correct
                    the errors before saving.

                </div>


                <!-- =====================================================
                     BUTTONS
                ====================================================== -->

                <div class="mt-7 flex flex-wrap gap-3">

                    <button
                        :disabled="form.processing"
                        type="submit"
                        class="rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        {{
                            form.processing
                                ? 'SAVING...'
                                : event
                                    ? 'UPDATE EVENT'
                                    : 'SAVE EVENT'
                        }}

                    </button>


                    <a
                        href="/admin/events"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"
                    >
                        CANCEL
                    </a>

                </div>

            </form>

        </div>

    </section>

</PublicLayout>

</template>