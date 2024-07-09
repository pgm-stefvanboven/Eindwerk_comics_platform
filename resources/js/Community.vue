<template>
    <div>
        <Header />
        <div class="container">
            <h1>Community</h1>
            <p>Welcome to the community page! Here you can interact with other users, share your thoughts, and discuss
                your favorite Marvel comics and characters.</p>
            <button @click="toggleNewTopicForm" class="btn btn-primary">Create New Topic</button>
            <div v-if="showNewTopicForm" class="form-container">
                <h2>Create a New Topic</h2>
                <form @submit.prevent="createTopic">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" v-model="newTopic.title" required />
                    </div>
                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea id="content" v-model="newTopic.content" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Topic</button>
                </form>
                <div v-if="error" class="error">{{ error }}</div>
            </div>
            <div class="topics-container">
                <h2>Topics:</h2>
                <ul>
                    <li v-for="topic in topics" :key="topic.id" class="topic-item">
                        <div>
                            <strong>{{ topic.title }}</strong>
                            <p>{{ topic.content }}</p>
                            <small>{{ formatDate(topic.created_at) }}</small>
                        </div>
                        <div class="buttons">
                            <button @click="editTopic(topic)" class="btn btn-secondary editicon">
                                <img src="images/edit.svg" alt="edit_icon">
                            </button>
                            <button @click="deleteTopic(topic.id)" class="btn btn-danger deleteicon">
                                <img src="images/delete.svg" alt="delete_icon">
                            </button>
                        </div>
                        <div v-if="topic.id === viewingRepliesFor" class="replies-container">
                            <h3>Replies</h3>
                            <ul>
                                <li v-for="reply in topic.replies" :key="reply.id">
                                    <p>{{ reply.content }}</p>
                                    <small>{{ formatDate(reply.created_at) }}</small>
                                    <div class="buttons">
                                        <button @click="editReply(reply)" class="btn btn-secondary editicon">
                                            <img src="images/edit.svg" alt="edit_icon">
                                        </button>
                                        <button @click="deleteReply(reply.id)" class="btn btn-danger deleteicon">
                                            <img src="images/delete.svg" alt="delete_icon">
                                        </button>
                                    </div>
                                </li>
                            </ul>
                            <form @submit.prevent="addReply(topic.id)">
                                <div class="form-group">
                                    <label for="replyContent">Your Reply:</label>
                                    <textarea id="replyContent" v-model="newReply" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary addicon">
                                    <img src="images/add-large-line.svg" alt="add_icon"> Add Reply
                                </button>
                            </form>
                        </div>
                        <button @click="viewReplies(topic.id)" class="btn btn-info">View Replies</button>
                    </li>
                </ul>
                <div v-if="editingTopic" class="edit-container">
                    <h2>Edit Topic</h2>
                    <form @submit.prevent="updateTopic">
                        <div class="form-group">
                            <label for="editTitle">Title:</label>
                            <input type="text" id="editTitle" v-model="editingTopic.title" required />
                        </div>
                        <div class="form-group">
                            <label for="editContent">Content:</label>
                            <textarea id="editContent" v-model="editingTopic.content" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Topic</button>
                        <button @click="cancelEdit" class="btn btn-secondary">Cancel</button>
                    </form>
                </div>

                <div v-if="editingReply" class="edit-container">
                    <h2>Edit Reply</h2>
                    <form @submit.prevent="updateReply">
                        <div class="form-group">
                            <label for="editReplyContent">Content:</label>
                            <textarea id="editReplyContent" v-model="editingReply.content" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Reply</button>
                        <button @click="cancelEditReply" class="btn btn-secondary">Cancel</button>
                    </form>
                </div>

            </div>
        </div>
        <Footer />
    </div>
</template>

<script>
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import axios from 'axios';

    export default {
        components: {
            Header,
            Footer
        },
        data() {
            return {
                topics: [],
                newTopic: {
                    title: '',
                    content: ''
                },
                newReply: '',
                editingTopic: null,
                editingReply: null,
                viewingRepliesFor: null,
                showNewTopicForm: false,
                error: ''
            };
        },
        mounted() {
            this.fetchTopics();
        },
        methods: {
            toggleNewTopicForm() {
                this.showNewTopicForm = !this.showNewTopicForm;
            },
            async fetchTopics() {
                try {
                    const response = await axios.get('/api/topics');
                    this.topics = response.data;
                } catch (error) {
                    this.error = 'Failed to fetch topics.';
                }
            },
            async createTopic() {
                try {
                    const response = await axios.post('/api/topics', this.newTopic);
                    this.topics.push(response.data);
                    this.newTopic.title = '';
                    this.newTopic.content = '';
                    this.showNewTopicForm = false;
                    this.error = '';
                } catch (error) {
                    this.error = 'Failed to create topic.';
                }
            },
            async updateTopic() {
                try {
                    const response = await axios.put(`/api/topics/${this.editingTopic.id}`, this.editingTopic);
                    const index = this.topics.findIndex(topic => topic.id === this.editingTopic.id);
                    this.topics.splice(index, 1, response.data);
                    this.editingTopic = null;
                    this.error = '';
                } catch (error) {
                    this.error = 'Failed to update topic.';
                }
            },
            async deleteTopic(id) {
                try {
                    await axios.delete(`/api/topics/${id}`);
                    this.topics = this.topics.filter(topic => topic.id !== id);
                    this.error = '';
                } catch (error) {
                    this.error = 'Failed to delete topic.';
                }
            },
            editTopic(topic) {
                this.editingTopic = { ...topic };
            },
            cancelEdit() {
                this.editingTopic = null;
            },
            formatDate(datetime) {
                return new Date(datetime).toLocaleString();
            },
            viewReplies(topicId) {
                if (this.viewingRepliesFor === topicId) {
                    this.viewingRepliesFor = null;
                } else {
                    this.viewingRepliesFor = topicId;
                }
            },
            async addReply(topicId) {
                try {
                    const response = await axios.post(`/api/topics/${topicId}/replies`, { content: this.newReply });
                    const topic = this.topics.find(t => t.id === topicId);
                    if (topic) {
                        topic.replies.push(response.data);
                    }
                    this.newReply = '';
                } catch (error) {
                    this.error = 'Failed to add reply.';
                }
            },

            async updateReply() {
                try {
                    const response = await axios.put(`/api/replies/${this.editingReply.id}`, this.editingReply);
                    const topic = this.topics.find(t => t.id === response.data.topic_id);
                    if (topic) {
                        const index = topic.replies.findIndex(reply => reply.id === response.data.id);
                        topic.replies.splice(index, 1, response.data);
                    }
                    this.editingReply = null;
                    this.error = '';
                } catch (error) {
                    this.error = 'Failed to update reply.';
                }
            },

            async deleteReply(id) {
                try {
                    await axios.delete(`/api/replies/${id}`);
                    this.topics.forEach(topic => {
                        topic.replies = topic.replies.filter(reply => reply.id !== id);
                    });
                    this.error = '';
                } catch (error) {
                    this.error = 'Failed to delete reply.';
                }
            },
            editReply(reply) {
                this.editingReply = { ...reply };
            },
            cancelEditReply() {
                this.editingReply = null;
            }
        }
    };
</script>

<style scoped>
    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 25px;
        text-align: center;
        background-color: #fdfdfd;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 40px;
        margin-bottom: 25px;
        color: #222;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #222;
    }

    p {
        font-size: 20px;
        margin-bottom: 30px;
        color: #555;
    }

    .form-container {
        margin-bottom: 50px;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        color: #333;
    }

    textarea {
        resize: vertical;
    }

    button {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        transition: background-color 0.3s;
    }

    .editicon,
    .deleteicon,
    .addicon {
        width: 30px;
        height: 30px;
        padding: 5px;
        border-radius: 5px;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-info:hover {
        background-color: #117a8b;
    }

    .topics-container {
        text-align: left;
        margin-top: 30px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    .topic-item {
        margin-bottom: 20px;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .topic-item div {
        margin-bottom: 10px;
    }

    .topic-item strong {
        font-size: 20px;
        color: #333;
    }

    .topic-item p {
        font-size: 18px;
        color: #555;
    }

    .buttons {
        display: flex;
        gap: 10px;
    }

    .edit-container {
        margin-top: 40px;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .error {
        color: red;
        margin-top: 10px;
    }

    .replies-container {
        margin-top: 10px;
        padding: 10px;
        background-color: #fafafa;
        border: 1px solid #ddd;
        border-radius: 10px;
    }

    .replies-container ul {
        padding: 0;
        list-style: none;
    }

    .replies-container li {
        background-color: #f1f1f1;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
</style>