
<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import { ArrowRight, CalendarDays, Clock3, MapPin } from 'lucide-vue-next';

defineProps({
    events: {
        type: Object,
        default: () => ({ data: [] })
    }
});

// Convert the database image path into a publicly accessible URL.
const eventImageUrl = (image) => {
    if (!image) return null;

    // Keep external image URLs unchanged.
    if (/^https?:\/\//i.test(image)) {
        return image;
    }

    // Remove leading slashes and an existing storage prefix if present.
    let path = String(image).replace(/^\/+/, '');
    path = path.replace(/^storage\//, '');

    return `/storage/${path}`;
};
</script>

<template>
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-[#00351F]">
            <div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]">
                    BBHS Old Boys Association
                </p>

                <h1 class="mt-3 text-4xl font-extrabold text-white sm:text-5xl">
                    Events
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-white/70">
                    Reunions, meetings, gatherings and activities that bring the brotherhood together.
                </p>
            </div>
        </section>

        <!-- Events Listing -->
        <section class="bg-[#F7F9F7] py-14 lg:py-16">
            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <!-- Empty State -->
                <div
                    v-if="!events.data?.length"
                    class="rounded-2xl border bg-white p-12 text-center text-gray-500"
                >
                    No upcoming events yet.
                </div>

                <!-- Event Cards -->
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="event in events.data"
                        :key="event.id"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
                    >
                        <!-- Event Image -->
                        <div class="relative h-52 overflow-hidden bg-[#00351F]">
                            <img
                                v-if="event.image"
                                :src="eventImageUrl(event.image)"
                                :alt="event.title"
                                class="h-full w-full object-cover"
                                @error="$event.target.style.display = 'none'"
                            />

                            <div
                                v-else
                                class="flex h-full items-center justify-center text-white/60"
                            >
                                <CalendarDays :size="42" />
                            </div>

                            <!-- Event Date -->
                            <div
                                class="absolute left-4 top-4 flex h-16 w-16 flex-col items-center justify-center rounded-xl bg-white shadow-lg"
                            >
                                <span class="text-xl font-extrabold text-[#00351F]">
                                    {{ event.date }}
                                </span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#006B3C]">
                                    {{ event.month }}
                                </span>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="p-6">
                            <h2 class="text-xl font-extrabold text-[#00351F]">
                                {{ event.title }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-gray-500">
                                {{ event.excerpt }}
                            </p>

                            <div class="mt-4 space-y-2 text-xs text-gray-500">
                                <div
                                    v-if="event.location || event.venue"
                                    class="flex items-start gap-2"
                                >
                                    <MapPin :size="15" class="shrink-0 text-[#006B3C]" />

                                    <span>
                                        {{ event.venue || event.location }}
                                        <template v-if="event.venue && event.location">
                                            · {{ event.location }}
                                        </template>
                                    </span>
                                </div>

                                <div v-if="event.time" class="flex items-center gap-2">
                                    <Clock3 :size="15" class="text-[#006B3C]" />
                                    {{ event.time }}
                                </div>
                            </div>

                            <!-- View Event -->
                            <a
                                :href="`/events/${event.slug}`"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C] hover:text-[#00351F]"
                            >
                                View Event
                                <ArrowRight :size="16" />
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
