<template>
    <div class="modal-overlay" @click.self="close">
        <div class="modal">
            <h2>{{ comic.title }}</h2>
            <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title" class="comic-image">
            <button @click="close">Sluiten</button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['comic'],
    methods: {
        close() {
            this.$emit('close');
        }
    }
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
}

.comic-image {
    width: 50%;
}
</style>