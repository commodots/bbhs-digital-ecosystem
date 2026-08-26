<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';

import {
    Search,
    MapPin,
    BriefcaseBusiness,
    GraduationCap,
    ArrowRight,
    Users,
    Globe2,
    CalendarDays,
} from 'lucide-vue-next';

/*
|--------------------------------------------------------------------------
| Images
|--------------------------------------------------------------------------
| These images are stored inside:
|
| public/images/bbhs/
|
*/

const schoolGateImage = '/images/bbhs/school/bbhs-school-gate.png';

const featuredAlumni = [
    {
        name: 'Oyewole Oyekunle Seun',
        class: 'Class of 1995',
        location: 'Lagos, Nigeria',
        profession: 'BBHS Old Boy',
        image: '/images/bbhs/alumni/oyewole-seun.jpg',
    },

    {
        name: 'Akinkunmi Akinlade',
        class: 'BBHS Old Boy',
        location: 'Nigeria',
        profession: 'BBHS Old Boy',
        image: '/images/bbhs/alumni/akinkunmi-akinlade.jpg',
    },

    {
        name: 'Otunba Wole Okunowo',
        class: 'Class of 1993',
        location: 'Nigeria',
        profession: 'BBHS Old Boy',
        image: '/images/bbhs/alumni/otunba-wole-okunowo.jpg',
    },
];

const classSets = [
    'Class of 1980',
    'Class of 1985',
    'Class of 1990',
    'Class of 1995',
    'Class of 2000',
    'Class of 2005',
];

const locations = [
    'Nigeria',
    'United Kingdom',
    'United States',
    'Canada',
    'South Africa',
    'Ghana',
];
</script>


<template>

    <PublicLayout>

        <!-- =========================================================
             HERO
        ========================================================== -->

        <section class="relative overflow-hidden bg-[#00351F]">

            <!-- Background -->
            <div class="absolute inset-0">

                <img
                    :src="schoolGateImage"
                    alt="Baptist Boys' High School Abeokuta"
                    class="h-full w-full object-cover object-center"
                />

                <!-- Green overlay -->
                <div
                    class="absolute inset-0"
                    style="
                        background:
                            linear-gradient(
                                90deg,
                                rgba(0, 53, 31, 0.97) 0%,
                                rgba(0, 53, 31, 0.88) 40%,
                                rgba(0, 53, 31, 0.58) 70%,
                                rgba(0, 53, 31, 0.28) 100%
                            );
                    "
                ></div>

                <!-- Bottom fade -->
                <div
                    class="absolute inset-x-0 bottom-0 h-24"
                    style="
                        background:
                            linear-gradient(
                                to top,
                                rgba(0, 53, 31, 0.45),
                                transparent
                            );
                    "
                ></div>

            </div>


            <!-- Hero content -->

            <div
                class="relative mx-auto flex min-h-[390px] max-w-[1440px] items-center px-5 py-16 lg:px-10"
            >

                <div class="max-w-3xl">

                    <p
                        class="text-xs font-bold uppercase tracking-[0.28em] text-[#F1D36A]"
                    >
                        The BBHS Brotherhood
                    </p>


                    <h1
                        class="mt-4 text-4xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-5xl lg:text-6xl"
                    >
                        Our Alumni.

                        <span class="block text-[#16A34A]">
                            One Brotherhood.
                        </span>
                    </h1>


                    <p
                        class="mt-5 max-w-2xl text-base leading-7 text-white/85 sm:text-lg"
                    >
                        Connect with generations of Baptist Boys' High School
                        Old Boys around the world. Find classmates, reconnect
                        with old friends and strengthen the BBHS brotherhood.
                    </p>

                </div>

            </div>

        </section>


        <!-- =========================================================
             SEARCH / DIRECTORY
        ========================================================== -->

        <section class="relative bg-white">

            <div
                class="mx-auto -mt-8 max-w-[1100px] px-5 pb-2 lg:px-8"
            >

                <div
                    class="relative rounded-2xl border border-gray-200 bg-white p-5 shadow-xl sm:p-7"
                >

                    <!-- Heading -->

                    <div class="mb-6">

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]"
                        >
                            Alumni Directory
                        </p>

                        <h2
                            class="mt-1 text-2xl font-extrabold text-[#00351F]"
                        >
                            Find an Old Boy
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            Search for classmates and fellow BBHS Old Boys.
                        </p>

                    </div>


                    <!-- Search fields -->

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

                        <!-- Name -->

                        <div class="lg:col-span-2">

                            <label
                                class="mb-2 block text-xs font-bold text-gray-700"
                            >
                                Name
                            </label>

                            <div class="relative">

                                <Search
                                    :size="18"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                                />

                                <input
                                    type="text"
                                    placeholder="Search by name..."
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 outline-none transition focus:border-[#006B3C] focus:bg-white focus:ring-2 focus:ring-[#006B3C]/10"
                                />

                            </div>

                        </div>


                        <!-- Class -->

                        <div>

                            <label
                                class="mb-2 block text-xs font-bold text-gray-700"
                            >
                                Class Set
                            </label>

                            <select
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-600 outline-none transition focus:border-[#006B3C] focus:bg-white"
                            >

                                <option value="">
                                    All Classes
                                </option>

                                <option
                                    v-for="classSet in classSets"
                                    :key="classSet"
                                    :value="classSet"
                                >
                                    {{ classSet }}
                                </option>

                            </select>

                        </div>


                        <!-- Location -->

                        <div>

                            <label
                                class="mb-2 block text-xs font-bold text-gray-700"
                            >
                                Location
                            </label>

                            <select
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-600 outline-none transition focus:border-[#006B3C] focus:bg-white"
                            >

                                <option value="">
                                    Any Location
                                </option>

                                <option
                                    v-for="location in locations"
                                    :key="location"
                                    :value="location"
                                >
                                    {{ location }}
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- Buttons -->

                    <div class="mt-5 flex flex-wrap gap-3">

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#00532F]"
                        >
                            <Search :size="17" />

                            Search Alumni
                        </button>


                        <button
                            type="button"
                            class="rounded-lg border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             QUICK STATISTICS
        ========================================================== -->

        <section class="bg-white py-12 lg:py-14">

            <div class="mx-auto max-w-[1100px] px-5 lg:px-8">

                <div
                    class="grid grid-cols-2 overflow-hidden rounded-2xl border border-gray-200 sm:grid-cols-4"
                >

                    <!-- Alumni -->

                    <div
                        class="flex items-center gap-3 border-b border-gray-200 p-5 sm:border-b-0 sm:border-r"
                    >

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EEF8F2]"
                        >
                            <Users
                                :size="21"
                                class="text-[#006B3C]"
                            />
                        </div>

                        <div>

                            <p class="text-xl font-extrabold text-[#00351F]">
                                2,500+
                            </p>

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-gray-500"
                            >
                                Alumni
                            </p>

                        </div>

                    </div>


                    <!-- Countries -->

                    <div
                        class="flex items-center gap-3 border-b border-gray-200 p-5 sm:border-b-0 sm:border-r"
                    >

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#FFF8E5]"
                        >
                            <Globe2
                                :size="21"
                                class="text-[#D4A72C]"
                            />
                        </div>

                        <div>

                            <p class="text-xl font-extrabold text-[#00351F]">
                                25+
                            </p>

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-gray-500"
                            >
                                Countries
                            </p>

                        </div>

                    </div>


                    <!-- Class Sets -->

                    <div
                        class="flex items-center gap-3 border-r border-gray-200 p-5"
                    >

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EEF8F2]"
                        >
                            <GraduationCap
                                :size="21"
                                class="text-[#006B3C]"
                            />
                        </div>

                        <div>

                            <p class="text-xl font-extrabold text-[#00351F]">
                                100+
                            </p>

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-gray-500"
                            >
                                Class Sets
                            </p>

                        </div>

                    </div>


                    <!-- Events -->

                    <div
                        class="flex items-center gap-3 p-5"
                    >

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#FFF8E5]"
                        >
                            <CalendarDays
                                :size="21"
                                class="text-[#D4A72C]"
                            />
                        </div>

                        <div>

                            <p class="text-xl font-extrabold text-[#00351F]">
                                150+
                            </p>

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-gray-500"
                            >
                                Events
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             ALUMNI SPOTLIGHT
        ========================================================== -->

        <section class="bg-[#F7F9F7] py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <!-- Heading -->

                <div
                    class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
                >

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]"
                        >
                            Our Community
                        </p>

                        <h2
                            class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl"
                        >
                            Alumni Spotlight
                        </h2>

                        <p
                            class="mt-2 max-w-xl text-sm leading-6 text-gray-500"
                        >
                            Celebrating members of the BBHS family and the
                            contributions they continue to make.
                        </p>

                    </div>


                    <a
                        href="#directory"
                        class="inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                    >
                        View alumni directory

                        <ArrowRight :size="16" />
                    </a>

                </div>


                <!-- Cards -->

                <div class="grid gap-5 md:grid-cols-3">

                    <article
                        v-for="alumnus in featuredAlumni"
                        :key="alumnus.name"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >

                        <!-- Image -->

                        <div
                            class="relative h-[300px] overflow-hidden bg-gray-100 sm:h-[330px]"
                        >

                            <img
                                :src="alumnus.image"
                                :alt="alumnus.name"
                                class="h-full w-full object-cover object-top transition duration-500 hover:scale-105"
                            />

                        </div>


                        <!-- Content -->

                        <div class="p-5">

                            <h3
                                class="text-xl font-extrabold text-[#00351F]"
                            >
                                {{ alumnus.name }}
                            </h3>


                            <p
                                class="mt-1 text-sm font-semibold text-[#009C3F]"
                            >
                                {{ alumnus.class }}
                            </p>


                            <div class="mt-4 space-y-2">

                                <div
                                    class="flex items-center gap-2 text-xs text-gray-500"
                                >
                                    <MapPin
                                        :size="15"
                                        class="text-[#006B3C]"
                                    />

                                    {{ alumnus.location }}
                                </div>


                                <div
                                    class="flex items-center gap-2 text-xs text-gray-500"
                                >
                                    <BriefcaseBusiness
                                        :size="15"
                                        class="text-[#006B3C]"
                                    />

                                    {{ alumnus.profession }}
                                </div>

                            </div>


                            <a
                                href="#"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-[#006B3C]"
                            >
                                View Profile

                                <ArrowRight :size="16" />
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- =========================================================
             CLASS SETS
        ========================================================== -->

        <section
            id="directory"
            class="bg-white py-14 lg:py-16"
        >

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div class="text-center">

                    <p
                        class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]"
                    >
                        Find Your Classmates
                    </p>

                    <h2
                        class="mt-2 text-3xl font-extrabold text-[#00351F]"
                    >
                        Browse by Class Set
                    </h2>

                    <p
                        class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-500"
                    >
                        Reconnect with Old Boys from your school years and
                        strengthen relationships that span generations.
                    </p>

                </div>


                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    <a
                        v-for="classSet in classSets"
                        :key="classSet"
                        href="#"
                        class="group flex items-center justify-between rounded-xl border border-gray-200 bg-white p-5 transition hover:border-[#006B3C] hover:bg-[#F7F9F7] hover:shadow-md"
                    >

                        <div class="flex items-center gap-4">

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EEF8F2]"
                            >

                                <GraduationCap
                                    :size="21"
                                    class="text-[#006B3C]"
                                />

                            </div>


                            <div>

                                <p class="font-bold text-[#00351F]">
                                    {{ classSet }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    View classmates
                                </p>

                            </div>

                        </div>


                        <ArrowRight
                            :size="18"
                            class="text-gray-400 transition group-hover:translate-x-1 group-hover:text-[#006B3C]"
                        />

                    </a>

                </div>

            </div>

        </section>


        <!-- =========================================================
             GLOBAL BROTHERHOOD
        ========================================================== -->

        <section class="bg-[#F7F9F7] py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div
                    class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center"
                >

                    <!-- Text -->

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#006B3C]"
                        >
                            Global Brotherhood
                        </p>

                        <h2
                            class="mt-2 text-3xl font-extrabold text-[#00351F] sm:text-4xl"
                        >
                            BBHS Old Boys Around the World.
                        </h2>

                        <p
                            class="mt-4 text-sm leading-7 text-gray-600"
                        >
                            From Abeokuta to cities across Nigeria and around
                            the world, the BBHS brotherhood continues to grow.
                        </p>


                        <a
                            href="/membership"
                            class="mt-6 inline-flex items-center gap-2 rounded-lg bg-[#006B3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#00532F]"
                        >
                            Join the Brotherhood

                            <ArrowRight :size="17" />
                        </a>

                    </div>


                    <!-- Locations -->

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">

                        <div
                            v-for="location in locations"
                            :key="location"
                            class="rounded-xl border border-gray-200 bg-white p-5 text-center transition hover:-translate-y-1 hover:shadow-md"
                        >

                            <MapPin
                                :size="21"
                                class="mx-auto text-[#006B3C]"
                            />

                            <p
                                class="mt-3 text-sm font-bold text-[#00351F]"
                            >
                                {{ location }}
                            </p>

                            <p
                                class="mt-1 text-[10px] text-gray-400"
                            >
                                BBHS Old Boys
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             CTA
        ========================================================== -->

        <section class="bg-white py-14 lg:py-16">

            <div class="mx-auto max-w-[1200px] px-5 lg:px-8">

                <div
                    class="relative overflow-hidden rounded-2xl bg-[#00351F] px-6 py-12 text-center sm:px-10"
                >

                    <!-- Decorative circles -->

                    <div
                        class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[45px] border-white/5"
                    ></div>

                    <div
                        class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full border-[45px] border-[#D4A72C]/10"
                    ></div>


                    <!-- CTA -->

                    <div class="relative">

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em] text-[#D4A72C]"
                        >
                            Stay Connected
                        </p>


                        <h2
                            class="mt-3 text-3xl font-extrabold text-white sm:text-4xl"
                        >
                            Not Yet in the Directory?
                        </h2>


                        <p
                            class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-white/70"
                        >
                            Create your alumni profile, reconnect with
                            classmates and become part of the growing BBHS
                            digital brotherhood.
                        </p>


                        <a
                            href="/membership"
                            class="mt-7 inline-flex items-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3.5 text-sm font-extrabold text-[#00351F] transition hover:bg-[#E4BA42]"
                        >
                            JOIN BBHS

                            <ArrowRight :size="17" />
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </PublicLayout>

</template>