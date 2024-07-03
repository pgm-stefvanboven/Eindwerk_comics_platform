<template>
    <div class="topics-container">
        <h2>Topics</h2>
        <ul>
            <li v-for="topic in topics" :key="topic.id">
                <a href="#" @click.prevent="$emit('select-topic', topic.id)">{{ topic.title }}</a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['selectTopic'],
        data() {
            return {
                topics: []
            };
        },
        mounted() {
            // Vervang dit met een API-aanroep om topics op te halen
            this.topics = [
                { id: 1, title: 'Favorite Marvel Comics' },
                { id: 2, title: 'Best Marvel Characters' }
            ];
        }
    };
</script>

<style scoped>
    .topics-container {
        text-align: left;
        margin-top: 20px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
    }
</style>