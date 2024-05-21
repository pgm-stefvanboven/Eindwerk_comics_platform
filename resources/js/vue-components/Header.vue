<template>
  <header class="bg-yellow-600 py-4">
    <nav class="max-w-screen-2xl mx-auto flex items-center justify-between px-4">
      <h1 class="text-3xl font-bold text-white"><a href="#" class="hover:text-yellow-300">Comic Universe</a></h1>
      <ul class="flex space-x-4 text-white">
        <li><router-link to="/series" class="hover:text-yellow-300">Series</router-link></li>
        <li><a href="#" class="hover:text-yellow-300">About</a></li>
        <li><a href="#" class="hover:text-yellow-300">Contact</a></li>
        <!-- Logout button -->
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
      isLoggedIn: false,
      username: null // Username of logged in user
    };
  },
  created() {
    // Verify that the user is logged in when the component is created
    this.checkLoginStatus();
  },
  methods: {
    checkLoginStatus() {
      // Check if the user is logged in via Laravel
      axios.get('/user')
        .then(response => {
          if (response.data) {
            this.isLoggedIn = true; // User is logged in
            this.username = response.data.username; // Retrieve username from response
          } else {
            this.isLoggedIn = false; // User is not logged in
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
          // Remove the access token from local storage
          localStorage.removeItem('accessToken');
          // Set the isLoggedIn status to false
          this.isLoggedIn = false;
          // Redirect to the login page after logging out
          window.location.replace('/login');
        })
        .catch(error => {
          console.error('Error logging out:', error);
        });
    }
  }
};
</script>