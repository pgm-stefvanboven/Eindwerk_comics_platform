<template>
    <div>
        <Header />
        <section class="comic-detail">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="detail-title">Comic Detail</h1>

                        <div v-if="loading">
                            <p>Loading comic details...</p>
                        </div>

                        <div v-else-if="error">
                            <p>{{ error }}</p>
                        </div>

                        <div v-else>
                            <div class="comic-card">
                                <img v-if="comic.thumbnail" :src="`/storage/${comic.thumbnail}`" alt="Comic thumbnail"
                                    class="comic-thumbnail">
                                <div class="comic-info">
                                    <h2>{{ comic.title }}</h2>
                                    <p>{{ comic.description }}</p>
                                    <p><strong>ID:</strong> {{ comic.id }}</p>
                                    <p><strong>Publisher:</strong> {{ comic.publisher }}</p>
                                    <p><strong>Price:</strong> {{ comic.price }}</p>
                                    <p><strong>Poster:</strong> {{ comic.poster }}</p>

                                    <p>
                                        <strong>Status: </strong>
                                        <span v-if="comic.type === 'sale'">Bought</span>
                                        <span v-else-if="comic.type === 'rent'">
                                            Rented
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="private-note">
                                <h3 class="note-title">Note</h3>
                                <textarea v-model="noteContent" @input="handleInput"
                                    placeholder="Write your note here..."></textarea>
                                <div class="note-actions">
                                    <button v-if="showSaveButton" @click="saveNote" class="btn_save">Save Note</button>
                                    <button v-if="noteExists" @click="deleteNote" class="btn_danger">Delete Note</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Footer />
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';

    export default {
        components: {
            Header,
            Footer
        },
        data() {
            return {
                comic: null,
                noteContent: '',
                noteExists: false,
                loading: true,
                error: null,
                showSaveButton: false, // New state for controlling the visibility of the Save button
            };
        },
        props: {
            id: {
                type: String,
                required: true,
            }
        },
        methods: {
            fetchComicDetails() {
                axios.get(`/api/comic/${this.id}`)
                    .then(response => {
                        this.comic = response.data.comic;
                        this.noteContent = response.data.note ? response.data.note.note : '';
                        this.noteExists = !!response.data.note;
                    })
                    .catch(error => {
                        console.error("Error fetching comic details:", error);
                        this.error = "Failed to load comic details.";
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            saveNote() {
                axios.post(`/api/comic/${this.id}/note`, {
                    note: this.noteContent
                })
                    .then(response => {
                        this.noteExists = true;
                        this.showSaveButton = false; // Hide the Save button after saving
                        alert('Note saved successfully');
                    })
                    .catch(error => {
                        console.error("Error saving note:", error);
                        alert('Failed to save note');
                    });
            },
            deleteNote() {
                axios.delete(`/api/comic/${this.id}/note`)
                    .then(response => {
                        this.noteContent = '';
                        this.noteExists = false;
                        this.showSaveButton = false; // Hide the Save button after deleting
                        alert('Note deleted successfully');
                    })
                    .catch(error => {
                        console.error("Error deleting note:", error);
                        alert('Failed to delete note');
                    });
            },
            handleInput() {
                this.showSaveButton = this.noteContent.length > 0;
            }
        },
        mounted() {
            this.fetchComicDetails();
        }
    };
</script>

<style scoped>
    .comic-detail {
        padding: 100px 0;
        text-align: center;
    }

    .detail-title {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .note-title {
        font-size: 1.5rem;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .comic-card {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        text-align: left;
    }

    .comic-thumbnail {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .comic-info {
        padding: 20px;
    }

    .comic-info h2 {
        margin-top: 0;
        font-size: 2rem;
    }

    .comic-info p {
        margin: 10px 0;
    }

    .private-note {
        max-width: 600px;
        margin: 0 auto;
        margin-top: 30px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .private-note textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }

    .note-actions {
        display: flex;
        gap: 10px;
    }

    .note-actions button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: #fff;
    }

    .note-actions button.btn_save {
        background: #007bff;
    }

    .note-actions button.btn_danger {
        background: #ff0000;
    }
</style>