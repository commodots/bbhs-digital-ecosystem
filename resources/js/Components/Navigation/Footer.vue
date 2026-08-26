<template>
    <footer
    class="text-white"
    style="background-color: #00351F;"
>

        <div class="mx-auto max-w-[1440px] px-6 py-14 lg:px-8">

            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

                <!-- Brand -->
                <div>
                    <img
						:src="'/images/bbhs/logo/bbhs-logo.png'"
						alt="BBHS Old Boys Association"
						class="mb-5 h-20 w-20 object-contain"
					>

                    <h3 class="text-lg font-bold">
                        Baptist Boys' High School
                    </h3>

                    <p class="text-sm font-semibold text-bbhs-gold-400">
                        Old Boys' Association
                    </p>

                    <p class="mt-4 max-w-sm text-sm leading-7 text-white/75">
                        United by heritage, committed to brotherhood,
                        and inspired to build a stronger future for
                        Baptist Boys' High School and generations to come.
                    </p>
                </div>

                <!-- Quick links -->
                <div>
                    <h3 class="font-bold">Quick Links</h3>

                    <div class="mt-5 space-y-3 text-sm text-white/75">
                        <a href="/about" class="block hover:text-bbhs-gold-300">
                            About Us
                        </a>

                        <a href="/alumni" class="block hover:text-bbhs-gold-300">
                            Alumni
                        </a>

                        <a href="/events" class="block hover:text-bbhs-gold-300">
                            Events & Reunions
                        </a>

                        <a href="/projects" class="block hover:text-bbhs-gold-300">
                            Projects
                        </a>
                    </div>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="font-bold">Resources</h3>

                    <div class="mt-5 space-y-3 text-sm text-white/75">
                        <a href="/news" class="block hover:text-bbhs-gold-300">
                            News & Media
                        </a>

                        <a href="/gallery" class="block hover:text-bbhs-gold-300">
                            Gallery
                        </a>

                        <a href="/constitution" class="block hover:text-bbhs-gold-300">
                            Constitution
                        </a>

                        <a href="/faqs" class="block hover:text-bbhs-gold-300">
                            FAQs
                        </a>
                    </div>
                </div>

                <!-- Member portal -->
                <div>
                    <h3 class="font-bold">Member Portal</h3>

                    <p class="mt-5 text-sm leading-6 text-white/75">
                        Access your profile, membership information,
                        events, connections and exclusive resources.
                    </p>

                    <a
                        href="/login"
                        class="mt-6 inline-block rounded-lg bg-bbhs-gold-500 px-5 py-3 text-sm font-bold text-bbhs-950 transition hover:bg-bbhs-gold-400"
                    >
                        MEMBER LOGIN
                    </a>
                </div>

            </div>

            <div class="mt-12 border-t border-green-800 pt-6 text-sm text-green-200">
                © {{ new Date().getFullYear() }} BBHS Old Boys Association.
                All Rights Reserved.
            </div>

        </div>

    </footer>
</template>