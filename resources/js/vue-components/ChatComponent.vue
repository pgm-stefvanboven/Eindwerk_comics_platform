<template>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Je chat met: {{ poster }}</h2>
            <button @click="closeChat">X</button>
        </div>
        <div class="chat-messages">
            <ul>
                <li v-for="chat in chats" :key="chat.id" :class="{'user-message': chat.poster === 'You'}">
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
        props: ['poster', 'listings'],
        data() {
            return {
                message: '',
                chats: []
            };
        },
        mounted() {
            this.fetchMessages();
        },
        watch: {
            poster(newPoster, oldPoster) {
                if (newPoster !== oldPoster) {
                    this.fetchMessages();
                }
            }
        },
        methods: {
            fetchMessages() {
                axios.get(`/api/chats/poster/${this.poster}`).then(response => {
                    console.log('Fetched messages:', response.data);
                    this.chats = response.data;
                }).catch(error => {
                    console.error('Error fetching messages:', error);
                });
            },
            sendMessage() {
                if (this.message.trim() === '') return;

                const newMessage = { poster: 'You', message: this.message };
                axios.post('/api/chats', newMessage)
                    .then(response => {
                        this.chats.push(response.data);
                        this.generateReply(this.message);
                        this.message = '';
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    });
            },
            generateReply(userMessage) {
                setTimeout(() => {
                    const replyMessage = {
                        poster: this.poster,
                        message: this.getDynamicReply(userMessage)
                    };
                    axios.post('/api/chats', replyMessage).then(response => {
                        console.log('Reply message:', response.data);
                        this.chats.push(response.data);
                    });
                }, 2000);
            },
            getDynamicReply(userMessage) {
                const lowerCaseMessage = userMessage.toLowerCase();

                if (lowerCaseMessage.includes("reserveren")) {
                    return "Bedankt! Ik kan de strip voor je reserveren.";
                }
                if (lowerCaseMessage.includes("prijs")) {
                    return `De prijs is €${this.getListingPrice()}. Is dat oké voor jou?`;
                }
                if (lowerCaseMessage.includes("beschikbaar")) {
                    return "De strip is nog beschikbaar. Wil je hem kopen?";
                }
                if (lowerCaseMessage.includes("foto's") || lowerCaseMessage.includes("foto's sturen")) {
                    return "Ik kan je meer foto's sturen als je wilt.";
                }
                if (lowerCaseMessage.includes("staat")) {
                    return "De strip is in goede staat.";
                }
                if (lowerCaseMessage.includes("ophalen")) {
                    return "Wanneer wil je de strip ophalen?";
                }
                if (lowerCaseMessage.includes("verzenden")) {
                    return "We kunnen ook verzenden als dat makkelijker is voor je.";
                }
                if (lowerCaseMessage.includes("betalen")) {
                    return "Hoe wil je betalen? Contant of via overschrijving?";
                }
                if (lowerCaseMessage.includes("vragen")) {
                    return "Laat me weten als je nog vragen hebt.";
                }
                return "Dank je voor je interesse in mijn strip!";
            },
            getListingPrice() {
                const listing = this.listings.find(listing => listing.poster === this.poster);
                return listing ? listing.price : 'onbekend';
            },
            closeChat() {
                this.$emit('close');
            }
        },
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

    .chat-messages li.user-message {
        background: #e0f7fa;
    }

    .chat-messages li button {
        margin-left: 1em;
        background: none;
        border: none;
        color: #007BFF;
        cursor: pointer;
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