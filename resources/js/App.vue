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
    </div>
</template>

<script>
import axios from 'axios';
import ComicModal from './ComicModal.vue';

export default {
    components: {
        ComicModal
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
</style>