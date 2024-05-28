<template>
  <header class="bg-yellow-600 py-4">
    <nav class="max-w-screen-2xl mx-auto flex items-center justify-between px-4">
      <h1 class="text-3xl font-bold text-white"><a href="/" class="hover:text-yellow-300">Marvel Comic Universe</a></h1>
      <input type="text" placeholder="Search" class="p-2 rounded-lg" />
      <ul class="flex space-x-4 text-white">
        <li><router-link to="/comics" class="text hover:text-yellow-300">Comics</router-link></li>
        <li><router-link to="/characters" class="text hover:text-yellow-300">Characters</router-link></li>
        <li><a href="#" class="text hover:text-yellow-300">About</a></li>
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

<style scoped>
.text {
  font-size: 1.5rem;
}
</style>