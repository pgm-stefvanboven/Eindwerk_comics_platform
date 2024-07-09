<template>
    <div>
        <Header />
        <div class="container">
            <h1>Community</h1>
            <p>Welcome to the community page! Here you can interact with other users, share your thoughts, and discuss
                your favorite Marvel comics and characters.</p>
            <button @click="toggleNewTopicForm" class="btn btn-primary addicon">
                <img src="images/add-large-line.svg" alt="add_icon">
                Create New Topic
            </button>
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
                        <div class="replies-container">
                            <h3>Replies:</h3>
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
                            <button @click="toggleReplyForm(topic.id)" class="btn btn-primary addicon">
                                <img src="images/add-large-line.svg" alt="add_icon"> Add Reply
                            </button>
                            <form v-if="showReplyForm === topic.id" @submit.prevent="addReply(topic.id)">
                                <div class="form-group">
                                    <label for="replyContent">Your Reply:</label>
                                    <textarea id="replyContent" v-model="newReply" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Reply</button>
                            </form>
                        </div>
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
                showNewTopicForm: false,
                showReplyForm: null,
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
            toggleReplyForm(topicId) {
                this.showReplyForm = this.showReplyForm === topicId ? null : topicId;
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
            async addReply(topicId) {
                try {
                    const response = await axios.post(`/api/topics/${topicId}/replies`, { content: this.newReply });
                    const topic = this.topics.find(t => t.id === topicId);
                    if (topic) {
                        topic.replies.push(response.data);
                    }
                    this.newReply = '';
                    this.showReplyForm = null;
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
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn {
        font-size: 16px;
        padding: 10px 10px;
        cursor: pointer;
    }

    .topics-container {
        margin-top: 30px;
    }

    .topics-container ul {
        list-style: none;
        padding: 0;
    }

    .topic-item {
        margin-bottom: 30px;
        padding: 20px;
        background-color: #f5f5f5;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .topic-item strong {
        font-size: 22px;
        color: #333;
    }

    .topic-item p {
        font-size: 18px;
        color: #555;
    }

    .topic-item small {
        font-size: 14px;
        color: #999;
    }

    .replies-container {
        margin-top: 20px;
        text-align: left;
    }

    .replies-container h3 {
        font-size: 20px;
        color: #222;
        margin-bottom: 10px;
    }

    .replies-container ul {
        list-style: none;
        padding: 0;
    }

    .replies-container ul li {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #eaeaea;
        border-radius: 5px;
    }

    .replies-container ul li p {
        font-size: 16px;
        color: #333;
    }

    .replies-container ul li small {
        font-size: 12px;
        color: #777;
    }

    .edit-container {
        margin-top: 30px;
        padding: 20px;
        background-color: #f5f5f5;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .error {
        color: red;
        margin-top: 10px;
    }

    .editicon img,
    .deleteicon img,
    .addicon img {
        width: 20px;
        height: 20px;
    }

    .addicon {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-group textarea {
        resize: none;
    }
</style>