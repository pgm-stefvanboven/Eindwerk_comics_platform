<template>
    <div>
        <router-view></router-view>
        <div class="comic-cards">
            <h1>Marvel Comics</h1>
            <div v-if="isLoggedIn" class="username">
                Welkom: {{ username }}
            </div>
            <div class="comic-list">
                <div v-for="comic in comics" :key="comic.id" class="comic-card" @click="selectComic(comic)">
                    <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title"
                        class="comic-image">
                    <div class="comic-details">
                        <h3 class="comic-title">{{ comic.title }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <comic-modal v-if="selectedComic" :comic="selectedComic" @close="selectedComic = null"></comic-modal>

        <div class="about">
            <h2 class="about-title">About ComicVerse</h2>
            <div class="about-content">
                <div class="about-content-left">
                    <img src="/images/Eindwerk_logo.svg" alt="ComicVerse Logo" class="about-logo">
                </div>

                <div class="about-content-right">
                    <p class="about-description">
                        ComicVerse is an online platform where you can explore and read the latest Marvel comics. We
                        have a vast collection of the best Marvel comics available for online reading. Sign up to gain
                        access to our collection and start reading today!
                    </p>
                </div>
            </div>
        </div>

        <div class="contact">
            <div class="contact-content">
                <div class="contact-info">
                    <h2 class="contact-title">Contact</h2>
                    <ul>
                        <li>Email: example@email.com</li>
                        <li>Telefoon: 123-456-789</li>
                        <li>Adres: Straatnaam 123, Stad, Land</li>
                    </ul>
                </div>

                <div class="contact-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit">Send</button>
                </div>
            </div>

            <div class="contact-image">
                <img src="/images/contact.png" alt="Contact" class="contact-image">
            </div>
        </div>

        <Footer />
    </div>
</template>

<script>
import axios from 'axios';
import ComicModal from './ComicModal.vue';
import Footer from './vue-components/Footer.vue';

export default {
    components: {
        ComicModal,
        Footer
    },
    data() {
        return {
            comics: [],
            isLoggedIn: false,
            username: null,
            selectedComic: null
        };
    },
    created() {
        this.fetchComics();
        this.checkLoginStatus();
    },
    methods: {
        fetchComics() {
            axios.get('https://gateway.marvel.com/v1/public/comics?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.comics = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching Marvel comics:', error);
                });
        },
        checkLoginStatus() {
            const accessToken = localStorage.getItem('accessToken');
            if (accessToken) {
                // Als er een toegangstoken wordt gevonden, ga ervan uit dat de gebruiker is ingelogd
                this.isLoggedIn = true;
                // Je kunt ervoor kiezen om andere gebruikersgegevens op te halen en in te stellen, zoals de gebruikersnaam
                // In dit voorbeeld stel ik alleen de inlogstatus in
                return;
            }

            // Als er geen toegangstoken wordt gevonden, controleer dan via het gebruikersendpoint
            axios.get('/user')
                .then(response => {
                    if (response.data) {
                        this.isLoggedIn = true; // Gebruiker is ingelogd
                        this.username = response.data.username; // Gebruikersnaam ophalen uit de respons
                    } else {
                        this.isLoggedIn = false; // Gebruiker is niet ingelogd
                    }
                })
                .catch(error => {
                    console.error('Error checking login status:', error);
                });
        },
        selectComic(comic) {
            this.selectedComic = comic;
        }

    }
};
</script>

<style scoped>
.comic-cards {
    max-width: 1536px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 2rem;
    margin-bottom: 20px;
}

h2 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.about {
    margin-top: 50px;
    padding: 20px;
}

.about-title {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
}

.about-content {
    display: flex;
    align-items: center;
}

.about-content-left {
    flex: 0 0 30%;
}

.about-logo {
    width: 100%;
    max-width: 200px;
    display: block;
    margin: 0 auto;
}

.about-content-right {
    max-width: 53%;
    flex: 1;
    padding: 20px;
}

.about-description {
    font-size: 18px;
    line-height: 1.5;
    color: #555;
}

.comic-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.comic-card {
    background-color: #f0f0f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.comic-card:hover {
    transform: scale(1.05);
}

.comic-image {
    width: 100%;
    height: 79%;
}

.comic-details {
    padding: 20px;
}

.comic-title {
    margin-top: 0;
}

.username {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.contact {
    display: flex;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.contact-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.contact-title {
    color: #333;
    font-size: 24px;
    margin-bottom: 10px;
}

.contact-info ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.contact-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-group textarea {
    resize: vertical;
}

button[type="submit"] {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.contact-image {
    margin-left: 10px;
    flex: 0 0 30%;
    margin-top: 8%;
}
</style>