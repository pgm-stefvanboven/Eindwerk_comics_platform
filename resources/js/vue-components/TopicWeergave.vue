<template>
    <div class="topic-container">
        <button @click="$emit('back')">Back to Topics</button>
        <h2>{{ topic.title }}</h2>
        <p>{{ topic.content }}</p>
        <div class="replies">
            <h3>Replies</h3>
            <ul>
                <li v-for="reply in replies" :key="reply.id">
                    <p>{{ reply.content }}</p>
                </li>
            </ul>
            <form @submit.prevent="addReply">
                <div>
                    <textarea v-model="newReply" required></textarea>
                </div>
                <button type="submit">Add Reply</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['topicId'],
        data() {
            return {
                topic: {},
                replies: [],
                newReply: ''
            };
        },
        mounted() {
            // Vervang dit met een API-aanroep om het topic en de reacties op te halen
            this.topic = { id: this.topicId, title: 'Example Topic', content: 'This is an example topic.' };
            this.replies = [
                { id: 1, content: 'Great topic!' },
                { id: 2, content: 'Thanks for sharing!' }
            ];
        },
        methods: {
            addReply() {
                // Voeg hier de API-aanroep toe om een nieuwe reactie toe te voegen
                console.log('New reply added:', this.newReply);
            }
        }
    };
</script>

<style scoped>
    .topic-container {
        text-align: left;
        margin-top: 20px;
    }

    .replies {
        margin-top: 20px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    textarea {
        margin-top: 5px;
    }

    button {
        margin-top: 20px;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>