<template>
  <header class="header">
    <div class="container">
      <nav class="nav">
        <div class="nav-left">
          <h1 class="logo">
            <a href="/" class="logo-link">Marvel Comic Universe</a>
          </h1>
          <input type="text" placeholder="Search" class="search-input" v-model="searchQuery" @input="search" />
        </div>
        <div class="nav-right">
          <ul class="nav-list">
            <li><router-link to="/comics" class="nav-item text">Comics</router-link></li>
            <li><router-link to="/characters" class="nav-item text">Characters</router-link></li>
            <li><router-link to="/community" class="nav-item text">Community</router-link></li>
            <li><router-link to="/store" class="nav-item text">Store</router-link></li>
            <li><router-link to="/swap-request" class="nav-item text">Requests</router-link></li>
            <li class="dropdown">
              <a href="#" class="nav-item text dropdown-toggle">More</a>
              <ul class="dropdown-menu">
                <li><router-link to="/wishlist" class="nav-item text">Wishlist</router-link></li>
                <li><router-link to="/collection" class="nav-item text">My Collection</router-link></li>
              </ul>
            </li>
          </ul>
          <button class="menu-toggle" @click="toggleMenu">☰</button>
        </div>
      </nav>
      <div v-if="isMenuOpen" class="mobile-menu">
        <ul class="nav-list">
          <li><router-link to="/comics" class="nav-item text">Comics</router-link></li>
          <li><router-link to="/characters" class="nav-item text">Characters</router-link></li>
          <li><router-link to="/wishlist" class="nav-item text">Wishlist</router-link></li>
          <li><router-link to="/community" class="nav-item text">Community</router-link></li>
          <li><router-link to="/store" class="nav-item text">Store</router-link></li>
          <li><router-link to="/swap-request" class="nav-item text">Requests</router-link></li>
          <li><router-link to="/collection" class="nav-item text">My Collection</router-link></li>
        </ul>
      </div>
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
        searchResults: [], // Search results to be displayed
        isMenuOpen: false // Mobile menu toggle
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
      toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen;
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
    padding: 1rem 0.8rem;
    position: relative;
  }

  .container {
    max-width: 1536px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
  }

  .nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .nav-left {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
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

  .menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #ffffff;
    font-size: 1.875rem;
    cursor: pointer;
  }

  .mobile-menu {
    display: none;
  }

  /* Media query 768px */
  @media (max-width: 768px) {
    .nav-right {
      display: none;
    }

    .menu-toggle {
      display: block;
    }

    .mobile-menu {
      display: block;
      position: absolute;
      top: 100%;
      right: 0;
      background-color: #d97706;
      width: 100%;
      padding: 1rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .nav-list {
      flex-direction: column;
      gap: 0.75rem;
    }
  }

  /* Media query 480px */
  @media (max-width: 480px) {
    .logo {
      font-size: 1.5rem;
    }

    .text {
      font-size: 1.25rem;
    }
  }

  /* Dropdown styles */
  .dropdown {
    position: relative;
  }

  .dropdown-toggle {
    cursor: pointer;
  }

  .dropdown-menu {
    display: none;
    position: absolute;
    text-align: center;
    top: 100%;
    left: -70px;
    background-color: #d97706;
    padding: 0.5rem 0;
    list-style-type: none;
    margin: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .dropdown-menu li {
    padding: 0.5rem 1rem;
  }

  .dropdown-menu li a {
    color: #ffffff;
    text-decoration: none;
    display: block;
  }

  .dropdown-menu li a:hover {
    color: #fbbf24;
  }

  .dropdown:hover .dropdown-menu {
    display: block;
  }

  /* Media query 768px */
  @media (max-width: 768px) {
    .dropdown-menu {
      position: static;
      box-shadow: none;
    }

    .dropdown:hover .dropdown-menu {
      display: none;
    }
  }

  /* Media query 480px */
  @media (max-width: 480px) {
    .dropdown-menu li {
      padding: 0.5rem;
    }
  }
</style>