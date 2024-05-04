<template>
    <footer class="bg-yellow-600 py-8 mt-8">
        <div class="max-w-screen-xl mx-auto px-4 text-white flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <img src="/images/Eindwerk_logo.svg" alt="ComicVerse Logo" class="w-24 md:w-32">
            </div>
            <div class="mb-4 md:mb-0 md:ml-8">
                <h3 class="text-xl font-semibold mb-2">Over ons</h3>
                <p class="text-sm">
                    Comic Universe is een website die je de nieuwste informatie over Marvel Comics biedt. Bekijk de
                    nieuwste series en comics en blijf op de hoogte van het laatste nieuws.
                </p>
                <div class="mt-4">
                    <h3 class="text-xl font-semibold mb-2">Navigatie</h3>
                    <ul class="space-y-2">
                        <li><router-link to="/series" class="hover:text-yellow-300">Series</router-link></li>
                        <li><a href="#" class="hover:text-yellow-300">Over ons</a></li>
                        <li><a href="#" class="hover:text-yellow-300">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-sm">
                <p>Data provided by Marvel. © 2024 Marvel</p>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'Footer'
};
</script>