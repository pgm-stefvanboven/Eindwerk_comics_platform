<template>
  <header class="header">
    <div class="container">
      <nav class="nav">
        <h1 class="logo">
          <a href="/" class="logo-link">Marvel Comic Universe</a>
        </h1>
        <input type="text" placeholder="Search" class="search-input" v-model="searchQuery" @input="search" />
        <ul class="nav-list">
          <li><router-link to="/comics" class="nav-item text">Comics</router-link></li>
          <li><router-link to="/characters" class="nav-item text">Characters</router-link></li>
          <li><router-link to="/wishlist" class="nav-item text">Wishlist</router-link></li>
          <li><router-link to="/community" class="nav-item text">Community</router-link></li>
        </ul>
      </nav>
      <div v-if="searchResults.length > 0" class="search-results">
        <ul>
          <li v-for="result in searchResults" :key="result.id">
            <router-link :to="'/comic/' + result.id">{{ result.title }}</router-link>
          </li>
        </ul>
      </div>
    </div>
  </header>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'Header',
    data() {
      return {
        isLoggedIn: false,
        username: null, // Username of logged in user
        searchQuery: '', // Query entered in the search input
        searchResults: [] // Search results to be displayed
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
      },
      search() {
        if (this.searchQuery.length < 1) {
          this.searchResults = [];
          return;
        }

        const url = `https://gateway.marvel.com/v1/public/comics`;
        const params = {
          ts: 1,
          apikey: 'ccf79dfafd46da85f6b71be57781c2b3',
          hash: 'cb120ce706d12dc495b10953e8a640f8',
          titleStartsWith: this.searchQuery,
          limit: 10
        };

        axios.get(url, { params })
          .then(response => {
            if (response.data && response.data.data && response.data.data.results) {
              this.searchResults = response.data.data.results.map(comic => ({
                id: comic.id,
                title: comic.title
              }));
            } else {
              this.searchResults = [];
            }
          })
          .catch(error => {
            console.error('Error fetching search results:', error);
          });
      }
    }
  };
</script>

<style scoped>
  .header {
    background-color: #d97706;
    padding-top: 1rem;
    padding-bottom: 0.8rem;
    position: relative;
  }

  .container {
    max-width: 1536px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    /* Added */
  }

  .nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .logo {
    font-size: 1.875rem;
    font-weight: 700;
    color: #ffffff;
  }

  .logo-link {
    color: inherit;
    text-decoration: none;
  }

  .logo-link:hover {
    color: #fbbf24;
  }

  .search-input {
    padding: 0.5rem;
    border-radius: 0.5rem;
    border: 1px solid #fff6eb;
    width: 300px;
    background-color: #fee4c7;
  }

  .search-results {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 100%;
    background-color: #fee4c7;
    border: 1px solid #fff6eb;
    width: 100%;
    max-width: 600px;
    margin-top: 10px;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .search-results ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  .search-results li {
    padding: 10px;
    border-bottom: 1px solid #fff6eb;
  }

  .search-results li a {
    text-decoration: none;
    color: #333;
  }

  .search-results li:hover {
    background-color: #fff6eb;
  }

  .nav-list {
    display: flex;
    gap: 1rem;
    color: #ffffff;
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  .nav-item {
    color: inherit;
    text-decoration: none;
  }

  .nav-item:hover {
    color: #fbbf24;
  }

  .text {
    font-size: 1.5rem;
  }

  /* Media query 768px */
  @media (max-width: 768px) {
    .nav {
      flex-direction: column;
      align-items: flex-start;
    }

    .nav-list {
      flex-direction: column;
      gap: 0.75rem;
    }

    .search-input {
      width: 100%;
      margin-bottom: 1rem;
    }

    .logo {
      margin-bottom: 1rem;
    }
  }

  /* Media query 480px */
  @media (max-width: 480px) {
    .nav {
      flex-direction: column;
      align-items: flex-start;
    }

    .nav-list {
      flex-direction: column;
      gap: 0.5rem;
    }

    .search-input {
      width: 100%;
      margin-bottom: 0.75rem;
    }

    .logo {
      font-size: 1.5rem;
      margin-bottom: 0.75rem;
    }

    .text {
      font-size: 1.25rem;
    }
  }
</style>