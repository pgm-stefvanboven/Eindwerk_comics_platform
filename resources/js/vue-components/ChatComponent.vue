<template>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with {{ poster }}</h2>
            <button @click="$emit('close')">X</button>
        </div>
        <div class="chat-messages">
            <ul>
                <li v-for="chat in chats" :key="chat.id">
                    <strong>{{ chat.poster }}:</strong> {{ chat.message }}
                </li>
            </ul>
        </div>
        <div class="chat-input">
            <input v-model="message" @keyup.enter="sendMessage" placeholder="Your message">
            <button @click="sendMessage">Send</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['poster'],
        data() {
            return {
                message: '',
                chats: []
            };
        },
        mounted() {
            this.fetchMessages();
            setInterval(this.fetchMessages, 5000); // Haal elke 5 seconden nieuwe berichten op
        },
        methods: {
            fetchMessages() {
                axios.get('/api/chats').then(response => {
                    this.chats = response.data;
                });
            },
            sendMessage() {
                if (this.message.trim() === '') return;
                const newMessage = { poster: 'You', message: this.message };
                axios.post('/api/chats', newMessage).then(response => {
                    this.message = '';
                    this.fetchMessages();
                    this.generateReply();
                });
            },
            generateReply() {
                setTimeout(() => {
                    const replyMessage = { poster: this.poster, message: this.getRandomReply() };
                    axios.post('/api/chats', replyMessage).then(response => {
                        this.fetchMessages();
                    });
                }, 2000); // Simuleer een vertraging van 2 seconden voor de gegenereerde reactie
            },
            getRandomReply() {
                const replies = [
                    "Dank je voor je interesse in mijn strip!",
                    "De prijs is €{{ listing.price }}. Is dat oké voor jou?",
                    "De strip is nog beschikbaar. Wil je hem kopen?",
                    "Ik kan je meer foto's sturen als je wilt.",
                    "De strip is in goede staat.",
                    "Wanneer wil je de strip ophalen?",
                    "We kunnen ook verzenden als dat makkelijker is voor je.",
                    "Hoe wil je betalen? Contant of via overschrijving?",
                    "Laat me weten als je nog vragen hebt.",
                    "Bedankt! Ik kan de strip voor je reserveren."
                ];
                return replies[Math.floor(Math.random() * replies.length)];
            }
        }
    };
</script>

<style scoped>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #007BFF;
        color: white;
        padding: 0.5em;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .chat-header h2 {
        margin: 0;
        font-size: 1.2em;
    }

    .chat-header button {
        background: none;
        border: none;
        color: white;
        font-size: 1.5em;
        cursor: pointer;
    }

    .chat-messages {
        flex: 1;
        padding: 1em;
        overflow-y: auto;
        background-color: #f1f1f1;
    }

    .chat-messages ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .chat-messages li {
        margin-bottom: 1em;
        padding: 0.5em;
        background: white;
        border-radius: 5px;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 0.5em;
        background-color: white;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .chat-input input {
        flex: 1;
        padding: 0.5em;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 0.5em;
    }

    .chat-input button {
        padding: 0.5em 1em;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>