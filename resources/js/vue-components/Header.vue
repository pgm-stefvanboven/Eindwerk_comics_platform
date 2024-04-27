<template>
  <header class="bg-yellow-600 py-4">
    <nav class="max-w-screen-2xl mx-auto flex items-center justify-between px-4">
      <h1 class="text-3xl font-bold text-white"><a href="#" class="hover:text-yellow-300">Comic Universe</a></h1>
      <ul class="flex space-x-4 text-white">
        <li><a href="#" class="hover:text-yellow-300">Series</a></li>
        <li><a href="#" class="hover:text-yellow-300">About</a></li>
        <li><a href="#" class="hover:text-yellow-300">Chat</a></li>
        <li><a href="#" class="hover:text-yellow-300">Contact</a></li>
        <!-- Logout knop -->
        <li v-if="isLoggedIn">
          <form method="POST" action="/logout" @submit.prevent="logout">
            <button type="submit" class="hover:text-yellow-300">Logout</button>
          </form>
        </li>
      </ul>
    </nav>
  </header>
</template>

<script>
import axios from 'axios';
export default {
  name: 'Header',
  data() {
    return {
      isLoggedIn: false, // We zullen deze waarde dynamisch instellen op basis van de gebruikersstatus
      username: null // Gebruikersnaam van ingelogde gebruiker
    };
  },
  created() {
    // Controleren of de gebruiker is ingelogd wanneer de component wordt gemaakt
    this.checkLoginStatus();
  },
  methods: {
    checkLoginStatus() {
      // Controleer of de gebruiker is ingelogd via Laravel
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
    logout() {
      if (!this.isLoggedIn) {
        console.warn('User is not logged in.');
        return;
      }

      axios.post('/logout')
        .then(response => {
          console.log('Logout response:', response);
          // Verwijder de toegangstoken uit de lokale opslag
          localStorage.removeItem('accessToken');
          // Stel de isLoggedIn status in op false
          this.isLoggedIn = false;
          // Redirect naar de inlogpagina na uitloggen
          window.location.replace('/login');
        })
        .catch(error => {
          console.error('Error logging out:', error);
        });
    }
  }
};
</script>