<script setup>
import PublicLayout from '../../../Layouts/PublicLayout.vue';
import AdminQuickLinks from '../../../Components/AdminQuickLinks.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: { type: Object, default: null },
    sets: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    selectedScopes: { type: Array, default: () => [] },
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
    image_file: null,

    registration_open: Boolean(
        props.event?.registration_open
    ),

    published: Boolean(props.event?.published),
    scope_type: props.event?.scope_type ?? 'global',
    graduation_set_id: props.event?.graduation_set_id ?? '',
    branch_id: props.event?.branch_id ?? '',
    target_scopes: [...(props.selectedScopes ?? [])],
    requires_registration: props.event?.requires_registration ?? true,
    allow_non_branch_residents: props.event?.allow_non_branch_residents ?? true,
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

const imagePreview = ref(
    props.event?.image
        ? (String(props.event.image).startsWith('http')
            ? props.event.image
            : `/storage/${String(props.event.image).replace(/^\/+/, '')}`)
        : null
);

const handleImageUpload = (event) => {
    const file = event.target.files?.[0] ?? null;
    form.image_file = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
        form.image = '';
    }
};

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

        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/events/${props.event.id}`,
            {
                forceFormData: true,
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
            forceFormData: true,
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

    <AdminQuickLinks />
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


                    <!-- EVENT AUDIENCE / ORGANISATIONAL SCOPE -->
                    <div class="sm:col-span-2 rounded-xl border border-[#DCE8E0] bg-[#F7FAF8] p-5">
                        <h2 class="font-bold text-[#00351F]">Event Audience</h2>
                        <p class="mt-1 text-sm text-gray-500">Choose who this event is intended for.</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-2 text-sm"><input v-model="form.scope_type" type="radio" value="global"/> National / Global – all members</label>
                            <label class="flex items-center gap-2 text-sm"><input v-model="form.scope_type" type="radio" value="set"/> Graduation set</label>
                            <label class="flex items-center gap-2 text-sm"><input v-model="form.scope_type" type="radio" value="branch"/> Geographical branch</label>
                            <label class="flex items-center gap-2 text-sm"><input v-model="form.scope_type" type="radio" value="joint"/> Joint event – multiple sets/branches</label>
                        </div>
                        <div v-if="form.scope_type === 'set'" class="mt-4">
                            <label class="mb-2 block text-sm font-semibold">Graduation set</label>
                            <select v-model="form.graduation_set_id" class="w-full rounded-lg border-gray-200">
                                <option value="">Select graduation set</option>
                                <option v-for="set in sets" :key="set.id" :value="set.id">{{set.name}} ({{set.graduation_year}})</option>
                            </select>
                            <p v-if="form.errors.graduation_set_id" class="mt-1 text-xs text-red-600">{{form.errors.graduation_set_id}}</p>
                        </div>
                        <div v-if="form.scope_type === 'branch'" class="mt-4">
                            <label class="mb-2 block text-sm font-semibold">Branch</label>
                            <select v-model="form.branch_id" class="w-full rounded-lg border-gray-200">
                                <option value="">Select branch</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{branch.name}}</option>
                            </select>
                            <p v-if="form.errors.branch_id" class="mt-1 text-xs text-red-600">{{form.errors.branch_id}}</p>
                        </div>
                        <div v-if="form.scope_type === 'joint'" class="mt-4">
                            <p class="mb-2 text-sm font-semibold">Select participating sets and branches</p>
                            <div class="grid gap-2 sm:grid-cols-2">
                                <label v-for="set in sets" :key="'set-'+set.id" class="flex items-center gap-2 rounded-lg bg-white p-2 text-sm">
                                    <input v-model="form.target_scopes" type="checkbox" :value="'set:'+set.id"/> {{set.name}} ({{set.graduation_year}})
                                </label>
                                <label v-for="branch in branches" :key="'branch-'+branch.id" class="flex items-center gap-2 rounded-lg bg-white p-2 text-sm">
                                    <input v-model="form.target_scopes" type="checkbox" :value="'branch:'+branch.id"/> {{branch.name}}
                                </label>
                            </div>
                            <p v-if="form.errors.target_scopes" class="mt-1 text-xs text-red-600">{{form.errors.target_scopes}}</p>
                        </div>
                        <label v-if="form.scope_type === 'branch'" class="mt-4 flex items-center gap-2 text-sm">
                            <input v-model="form.allow_non_branch_residents" type="checkbox"/> Include approved members living in the branch's mapped cities, even if they are not formal branch members
                        </label>
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


                    <!-- EVENT IMAGE UPLOAD -->
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#00351F]">
                            Event Image
                        </label>
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            @change="handleImageUpload"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3 file:mr-4 file:rounded-md file:border-0 file:bg-[#EAF3ED] file:px-4 file:py-2 file:font-semibold file:text-[#006B3C]"
                        />
                        <p class="mt-1 text-xs text-gray-400">
                            Upload JPG, PNG or WebP. Maximum file size: 5 MB.
                        </p>
                        <p v-if="form.errors.image_file" class="mt-1 text-xs text-red-600">
                            {{ form.errors.image_file }}
                        </p>
                        <div v-if="imagePreview" class="mt-4 max-w-md">
                            <p class="mb-2 text-xs font-semibold text-gray-600">Image preview</p>
                            <img :src="imagePreview" alt="Event image preview" class="h-48 w-full rounded-xl border border-gray-200 object-cover" />
                        </div>
                        <div class="mt-4">
                            <label class="mb-2 block text-xs font-semibold text-gray-600">
                                Or use an existing image URL (optional)
                            </label>
                            <input
                                v-model="form.image"
                                type="url"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3"
                                placeholder="events/filename.jpg or https://example.com/image.jpg"
                            />
                            <p v-if="form.errors.image" class="mt-1 text-xs text-red-600">
                                {{ form.errors.image }}
                            </p>
                        </div>
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


                        <label class="flex items-center gap-3 text-sm font-semibold text-[#00351F]">
                            <input v-model="form.requires_registration" type="checkbox" class="rounded"/>
                            Require member registration
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