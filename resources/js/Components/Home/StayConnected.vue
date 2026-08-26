<script setup>
import {
    ArrowRight,
    Mail,
    Facebook,
    Instagram,
    Linkedin,
    Youtube,
} from 'lucide-vue-next';

const submitNewsletter = () => {
    // Laravel newsletter endpoint will be connected later.
};
</script>

<template>
    <section class="bg-[#f7f9f7] py-10 lg:py-12">
        <div class="mx-auto max-w-[1440px] px-5 lg:px-8">

            <div
                class="relative overflow-hidden rounded-2xl bg-[#00351F] px-6 py-8 shadow-sm sm:px-8 lg:px-10 lg:py-9"
            >

                <!-- Decorative elements -->

                <div
                    class="absolute -right-20 -top-24 h-64 w-64 rounded-full border-[40px] border-white/5"
                ></div>

                <div
                    class="absolute -bottom-28 -left-20 h-64 w-64 rounded-full border-[35px] border-[#D4A72C]/10"
                ></div>


                <div
                    class="relative flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between"
                >

                    <!-- Text -->

                    <div class="max-w-xl">

                        <p
                            class="text-xs font-bold uppercase tracking-[0.22em]"
                            style="color: #F1D36A;"
                        >
                            Stay Connected
                        </p>

                        <h2
                            class="mt-2 text-2xl font-extrabold text-white sm:text-3xl"
                        >
                            Keep Up With BBHS
                        </h2>

                        <p
                            class="mt-3 text-sm leading-6 text-white/70"
                        >
                            Get news, events, alumni updates and opportunities
                            delivered to your inbox.
                        </p>

                    </div>


                    <!-- Newsletter -->

                    <form
                        class="w-full max-w-xl"
                        @submit.prevent="submitNewsletter"
                    >

                        <label
                            for="newsletter-email"
                            class="sr-only"
                        >
                            Email address
                        </label>

                        <div
                            class="flex flex-col gap-3 sm:flex-row"
                        >

                            <div
                                class="flex min-w-0 flex-1 items-center gap-3 rounded-lg bg-white px-4 py-3"
                            >

                                <Mail
                                    :size="19"
                                    class="shrink-0 text-gray-400"
                                />

                                <input
                                    id="newsletter-email"
                                    type="email"
                                    required
                                    placeholder="Enter your email address"
                                    class="min-w-0 flex-1 border-0 bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0"
                                />

                            </div>


                            <button
                                type="submit"
                                class="group inline-flex items-center justify-center gap-2 rounded-lg bg-[#D4A72C] px-6 py-3 font-bold text-[#00351F] transition hover:bg-[#E5B93F]"
                            >
                                SUBSCRIBE

                                <ArrowRight
                                    :size="17"
                                    class="transition-transform group-hover:translate-x-1"
                                />
                            </button>

                        </div>

                        <p class="mt-2 text-[11px] text-white/45">
                            We respect your privacy. Unsubscribe at any time.
                        </p>

                    </form>

                </div>

            </div>


            <!-- Social row -->

            <div
                class="flex flex-col gap-4 py-6 sm:flex-row sm:items-center sm:justify-between"
            >

                <p class="text-sm text-gray-500">
                    Follow the BBHS Old Boys Association
                </p>


                <div class="flex items-center gap-2">

                    <a
                        href="#"
                        aria-label="Facebook"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:border-[#006b3c] hover:bg-[#006b3c] hover:text-white"
                    >
                        <Facebook :size="17" />
                    </a>

                    <a
                        href="#"
                        aria-label="Instagram"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:border-[#006b3c] hover:bg-[#006b3c] hover:text-white"
                    >
                        <Instagram :size="17" />
                    </a>

                    <a
                        href="#"
                        aria-label="LinkedIn"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:border-[#006b3c] hover:bg-[#006b3c] hover:text-white"
                    >
                        <Linkedin :size="17" />
                    </a>

                    <a
                        href="#"
                        aria-label="YouTube"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:border-[#006b3c] hover:bg-[#006b3c] hover:text-white"
                    >
                        <Youtube :size="17" />
                    </a>

                </div>

            </div>

        </div>
    </section>
</template>