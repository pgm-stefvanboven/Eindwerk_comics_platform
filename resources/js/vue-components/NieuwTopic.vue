<template>
    <div class="new-topic-container">
        <h2>Create New Topic</h2>
        <form @submit.prevent="createTopic">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" v-model="title" required />
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea id="content" v-model="content" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                title: '',
                content: ''
            };
        },
        methods: {
            createTopic() {
                // Voeg hier de API-aanroep toe om het nieuwe topic aan te maken
                console.log('New topic created:', this.title, this.content);
            }
        }
    };
</script>

<style scoped>
    .new-topic-container {
        text-align: left;
        margin-top: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-top: 10px;
    }

    input,
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