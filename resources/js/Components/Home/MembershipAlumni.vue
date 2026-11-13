<script setup>
import { ArrowRight, Award, Globe2, UsersRound } from 'lucide-vue-next';

const props = defineProps({ spotlights: { type: Array, default: () => [] } });
</script>

<template>
    <section class="bg-white py-12 lg:py-16">
        <div class="mx-auto max-w-[1440px] px-5 lg:px-8">

            <div class="grid gap-6 lg:grid-cols-12">

                <!-- =================================================
                     MEMBERSHIP CTA
                ================================================== -->

                <div
                    class="relative overflow-hidden rounded-2xl bg-[#00351F] px-7 py-8 text-white shadow-sm lg:col-span-5 lg:px-9 lg:py-10"
                >

                    <!-- Decorative circles -->

                    <div
                        class="absolute -right-16 -top-16 h-48 w-48 rounded-full border-[30px] border-white/5"
                    ></div>

                    <div
                        class="absolute -bottom-20 -left-16 h-48 w-48 rounded-full border-[25px] border-[#D4A72C]/10"
                    ></div>


                    <div class="relative">

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em]"
                            style="color: #F1D36A;"
                        >
                            Join the Brotherhood
                        </p>

                        <h2
                            class="mt-3 text-3xl font-extrabold leading-tight"
                        >
                            Not a Member Yet?
                        </h2>

                        <p
                            class="mt-4 max-w-md text-sm leading-7 text-white/75"
                        >
                            Join a global network of BBHS Old Boys committed
                            to preserving our heritage, strengthening lifelong
                            relationships and making a lasting impact.
                        </p>


                        <!-- Benefits -->

                        <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10"
                                >
                                    <UsersRound :size="18" />
                                </div>

                                <span class="text-sm font-medium">
                                    Global Brotherhood
                                </span>
                            </div>


                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10"
                                >
                                    <Globe2 :size="18" />
                                </div>

                                <span class="text-sm font-medium">
                                    Global Connections
                                </span>
                            </div>


                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10"
                                >
                                    <Award :size="18" />
                                </div>

                                <span class="text-sm font-medium">
                                    Exclusive Events
                                </span>
                            </div>


                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10"
                                >
                                    <ArrowRight :size="18" />
                                </div>

                                <span class="text-sm font-medium">
                                    Make an Impact
                                </span>
                            </div>

                        </div>


                        <!-- CTA -->

                        <a
                            href="/membership"
                            class="group mt-8 inline-flex items-center gap-3 rounded-lg px-6 py-3.5 text-sm font-bold shadow-lg transition hover:-translate-y-0.5"
                            style="background-color: #D4A72C; color: #00351F;"
                        >
                            JOIN BBHS NOW

                            <ArrowRight
                                :size="18"
                                class="transition-transform group-hover:translate-x-1"
                            />
                        </a>

                    </div>

                </div>


                <!-- =================================================
                     ALUMNI SPOTLIGHT
                ================================================== -->

                <div
                    class="rounded-2xl border border-gray-100 bg-[#f7f9f7] p-6 shadow-sm lg:col-span-7 lg:p-7"
                >

                    <div class="flex items-end justify-between gap-5">

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-[0.22em] text-[#006b3c]"
                            >
                                Our Community
                            </p>

                            <h2
                                class="mt-2 text-2xl font-extrabold text-[#00351F] sm:text-3xl"
                            >
                                Alumni Spotlight
                            </h2>

                        </div>

                        <a
                            href="/alumni"
                            class="hidden items-center gap-2 text-sm font-semibold text-[#006b3c] sm:flex"
                        >
                            Meet our alumni

                            <ArrowRight :size="17" />
                        </a>

                    </div>


                    <!-- Alumni cards -->

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">

                        <article
                            v-for="person in spotlights"
                            :key="person.id"
                            class="group overflow-hidden rounded-xl bg-white shadow-sm"
                        >

                            <div class="relative aspect-[4/5] overflow-hidden">

                                <img
                                    :src="person.image"
                                    :alt="person.name"
                                    class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-105"
                                />

                                <div
                                    class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-[#00351F]/80 to-transparent"
                                ></div>

                            </div>


                            <div class="p-4">

                                <a :href="person.profileUrl" class="text-sm font-bold text-[#00351F] hover:text-[#087A3A]">{{ person.name }}</a>
                                <p v-if="person.headline" class="mt-1 text-xs font-semibold text-[#087A3A]">{{ person.headline }}</p>
                                <p v-if="person.writeup" class="mt-2 line-clamp-3 text-xs leading-5 text-gray-600">{{ person.writeup }}</p>

                                <p
                                    class="mt-1 text-xs text-gray-500"
                                >
                                    {{ person.classSet }}
                                </p>

                            </div>

                        </article>

                    </div>


                    <p v-if="!spotlights.length" class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-500">Alumni spotlights will appear here when selected by the Association.</p>

                    <!-- Mobile alumni link -->

                    <a
                        href="/alumni"
                        class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold text-[#006b3c] sm:hidden"
                    >
                        Meet our alumni
                        <ArrowRight :size="17" />
                    </a>

                </div>

            </div>

        </div>
    </section>
</template>