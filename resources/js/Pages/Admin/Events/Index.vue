<script setup>

import PublicLayout from '../../../Layouts/PublicLayout.vue';

import {
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    Plus,
    Pencil,
    Trash2,
    CalendarDays,
    Clock3,
} from 'lucide-vue-next';


const props = defineProps({

    events: {
        type: Object,
        default: () => ({
            data: [],
        }),
    },

});


const page = usePage();


/*
|--------------------------------------------------------------------------
| Format event date/time
|--------------------------------------------------------------------------
*/

const formatDateTime = (value) => {

    if (!value) {
        return 'Date & time TBC';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Date & time TBC';
    }

    return date.toLocaleString('en-NG', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });

};


/*
|--------------------------------------------------------------------------
| Delete event
|--------------------------------------------------------------------------
*/

const destroy = (id) => {

    if (
        confirm(
            'Are you sure you want to delete this event?'
        )
    ) {

        useForm({})
            .delete(
                `/admin/events/${id}`,
                {
                    preserveScroll: true,
                }
            );

    }

};

</script>


<template>

<PublicLayout>


<!-- HEADER -->

<section class="bg-[#00351F]">

    <div
        class="mx-auto max-w-[1400px] px-5 py-12 lg:px-8"
    >

        <p
            class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]"
        >
            Administration
        </p>

        <div
            class="mt-2 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
        >

            <div>

                <h1
                    class="text-3xl font-extrabold text-white sm:text-4xl"
                >
                    Events
                </h1>

                <p
                    class="mt-2 text-sm text-white/70"
                >
                    Create and manage BBHS events.
                </p>

            </div>


            <a
                href="/admin/events/create"
                class="inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-5 py-3 text-sm font-bold text-[#00351F]"
            >

                <Plus :size="17" />

                NEW EVENT

            </a>

        </div>

    </div>

</section>


<!-- CONTENT -->

<section class="bg-[#F7F9F7] py-10">

    <div
        class="mx-auto max-w-[1400px] px-5 lg:px-8"
    >


        <!-- SUCCESS -->

        <div
            v-if="page.props.flash?.success"
            class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700"
        >

            {{ page.props.flash.success }}

        </div>


        <!-- EVENTS -->

        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white"
        >

            <div
                v-if="!events.data?.length"
                class="p-10 text-center text-sm text-gray-500"
            >

                No events yet.

            </div>


            <div
                v-else
                class="divide-y divide-gray-100"
            >


                <div
                    v-for="event in events.data"
                    :key="event.id"
                    class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between"
                >


                    <!-- EVENT INFO -->

                    <div
                        class="flex items-start gap-4"
                    >

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[#EEF8F2] text-[#006B3C]"
                        >

                            <CalendarDays :size="19" />

                        </div>


                        <div>

                            <h2
                                class="font-extrabold text-[#00351F]"
                            >
                                {{ event.title }}
                            </h2>


                            <!-- DATE / TIME / VENUE -->

                            <div
                                class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500"
                            >

                                <span
                                    v-if="event.starts_at"
                                    class="inline-flex items-center gap-1"
                                >

                                    <Clock3 :size="13" />

                                    {{ formatDateTime(event.starts_at) }}

                                </span>

                                <span v-else>
                                    Date & time TBC
                                </span>


                                <span>
                                    ·
                                </span>


                                <span>
                                    {{ event.venue || 'Venue TBC' }}
                                </span>


                                <span>
                                    ·
                                </span>


                                <span
                                    :class="
                                        event.published
                                            ? 'font-semibold text-green-700'
                                            : 'font-semibold text-amber-700'
                                    "
                                >

                                    {{
                                        event.published
                                            ? 'Published'
                                            : 'Draft'
                                    }}

                                </span>

                            </div>


                            <!-- LOCATION -->

                            <p
                                v-if="event.location"
                                class="mt-1 text-xs text-gray-400"
                            >

                                {{ event.location }}

                            </p>


                            <!-- EXCERPT -->

                            <p
                                v-if="event.excerpt"
                                class="mt-2 text-sm text-gray-500"
                            >

                                {{ event.excerpt }}

                            </p>

                        </div>

                    </div>


                    <!-- ACTIONS -->

                    <div
                        class="flex shrink-0 gap-2"
                    >

                        <a
                            :href="`/admin/events/${event.id}/edit`"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold hover:bg-gray-50"
                        >

                            <Pencil :size="14" />

                            EDIT

                        </a>


                        <button
                            @click="destroy(event.id)"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100"
                        >

                            <Trash2 :size="14" />

                            DELETE

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


</PublicLayout>

</template>