<div x-data="{ activeIndex: 0, images: @json($images) }" class="relative w-40 h-40">
    <!-- Gambar -->
    <template x-for="(image, index) in images" :key="index">
        <img :src="'/storage/' + image"
            class="absolute inset-0 w-full h-full object-cover rounded-lg transition-opacity duration-300"
            x-show="activeIndex === index">
    </template>

    <!-- Navigasi -->
    <button @click="activeIndex = (activeIndex - 1 + images.length) % images.length"
        class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white px-2 py-1 rounded-l">
        ‹
    </button>
    <button @click="activeIndex = (activeIndex + 1) % images.length"
        class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white px-2 py-1 rounded-r">
        ›
    </button>
</div>
