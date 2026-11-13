<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CalendarDays, Clock3, MapPin, Users, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps({
    event: { type: Object, required: true },
});

const page = usePage();
const registerForm = useForm({});
const cancelForm = useForm({});

const eventImageUrl = (image) => {
    if (!image) return null;
    if (/^https?:\/\//i.test(image)) return image;
    let path = String(image).replace(/^\/+/, '');
    path = path.replace(/^storage\//, '');
    return `/storage/${path}`;
};

const register = () => {
    registerForm.post(`/events/${props.event.slug}/register`, {
        preserveScroll: true,
    });
};

const cancelRegistration = () => {
    if (confirm('Are you sure you want to cancel your registration?')) {
        cancelForm.delete(`/events/${props.event.slug}/register`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <PublicLayout>
        <section class="relative min-h-[430px] overflow-hidden bg-[#00351F]">
            <img
                v-if="event.image"
                :src="eventImageUrl(event.image)"
                :alt="event.title"
                class="absolute inset-0 h-full w-full object-cover"
                @error="$event.target.style.display = 'none'"
            />
            <div class="absolute inset-0 bg-[#00351F]/80"></div>
            <div class="relative mx-auto flex min-h-[430px] max-w-[1200px] items-end px-5 py-14 lg:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">BBHS Old Boys Association | Event</p>
                    <h1 class="mt-3 max-w-4xl text-4xl font-extrabold text-white sm:text-5xl">{{ event.title }}</h1>
                </div>
            </div>
        </section>

        <section class="bg-white py-12">
            <div class="mx-auto max-w-[1100px] px-5 lg:px-8">
                <div v-if="page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                    {{ page.props.flash.success }}
                </div>
                <div v-if="registerForm.errors.event || registerForm.errors.registration" class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    {{ registerForm.errors.event || registerForm.errors.registration }}
                </div>

                <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
                    <article>
                        <p v-if="event.excerpt" class="text-lg leading-8 text-gray-600">{{ event.excerpt }}</p>
                        <h2 class="mt-8 text-2xl font-extrabold text-[#00351F]">About the Event</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-600">{{ event.description }}</p>
                        <a href="/events" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C] hover:text-[#00351F]">
                            <ArrowLeft :size="16" /> Back to Events
                        </a>
                    </article>

                    <aside class="h-fit rounded-2xl bg-[#F7F9F7] p-6">
                        <h3 class="mb-6 text-lg font-extrabold text-[#00351F]">Event Details</h3>
                        <div class="space-y-5">
                            <div v-if="event.starts_at" class="flex gap-3">
                                <CalendarDays class="shrink-0 text-[#006B3C]" :size="20" />
                                <div><b class="text-sm text-[#00351F]">Starts</b><p class="text-xs text-gray-500">{{ event.starts_at }}</p></div>
                            </div>
                            <div v-if="event.ends_at" class="flex gap-3">
                                <Clock3 class="shrink-0 text-[#006B3C]" :size="20" />
                                <div><b class="text-sm text-[#00351F]">Ends</b><p class="text-xs text-gray-500">{{ event.ends_at }}</p></div>
                            </div>
                            <div v-if="event.venue || event.location" class="flex gap-3">
                                <MapPin class="shrink-0 text-[#006B3C]" :size="20" />
                                <div><b class="text-sm text-[#00351F]">Location</b><p class="text-xs text-gray-500">{{ event.venue }}<span v-if="event.venue && event.location"> · </span>{{ event.location }}</p></div>
                            </div>
                            <div v-if="event.requires_registration" class="flex gap-3">
                                <Users class="shrink-0 text-[#006B3C]" :size="20" />
                                <div>
                                    <b class="text-sm text-[#00351F]">Registration</b>
                                    <p class="text-xs text-gray-500">
                                        {{ event.registration_count }} registered
                                        <span v-if="event.capacity"> · {{ event.remaining_capacity }} places remaining</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="event.requires_registration" class="mt-7 border-t border-gray-200 pt-5">
                            <div v-if="event.registration?.status === 'registered'" class="rounded-lg bg-green-50 p-4">
                                <p class="flex items-center gap-2 text-sm font-bold text-green-800"><CheckCircle2 :size="18" /> You are registered</p>
                                <button @click="cancelRegistration" :disabled="cancelForm.processing" class="mt-3 text-sm font-semibold text-red-700 underline disabled:opacity-50">
                                    {{ cancelForm.processing ? 'Cancelling...' : 'Cancel registration' }}
                                </button>
                            </div>
                            <template v-else-if="!event.is_authenticated">
                                <p class="text-sm text-gray-600">Sign in with your approved BBHS member account to register for this event.</p>
                                <a href="/login" class="mt-3 inline-flex w-full justify-center rounded-lg bg-[#00351F] px-4 py-3 text-sm font-bold text-white">Sign in to register</a>
                            </template>
                            <template v-else-if="!event.member_approved">
                                <p class="text-sm text-amber-800">Your BBHS membership must be approved before you can register.</p>
                            </template>
                            <template v-else-if="!event.registration_open">
                                <p class="rounded-lg bg-gray-100 p-3 text-sm text-gray-600">Registration is currently closed.</p>
                            </template>
                            <template v-else-if="event.remaining_capacity === 0">
                                <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800">This event has reached its registration capacity.</p>
                            </template>
                            <template v-else>
                                <button @click="register" :disabled="registerForm.processing" class="w-full rounded-lg bg-[#006B3C] px-4 py-3 text-sm font-bold text-white hover:bg-[#00351F] disabled:opacity-50">
                                    {{ registerForm.processing ? 'Registering...' : 'Register for this event' }}
                                </button>
                            </template>
                        </div>
                        <div v-else class="mt-7 rounded-lg bg-[#00351F] p-4 text-sm font-semibold text-white">
                            Registration is not required for this event.
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
