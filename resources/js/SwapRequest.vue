<template>
    <div>
        <Header />
        <div class="swap-container">
            <h2>Swap Requests</h2>
            <div class="filters">
                <button :class="{ active: filter === 'all' }" @click="filter = 'all'">All</button>
                <button :class="{ active: filter === 'accepted' }" @click="filter = 'accepted'">Accepted</button>
                <button :class="{ active: filter === 'rejected' }" @click="filter = 'rejected'">Rejected</button>
            </div>
            <div v-if="filteredSwaps.length">
                <div v-for="swap in filteredSwaps" :key="swap.id" class="swap-request">
                    <p><strong>Comic ID:</strong> {{ swap.comic_id }}</p>
                    <p><strong>Requested Comic ID:</strong> {{ swap.requested_comic_id }}</p>
                    <p><strong>Status:</strong> {{ swap.status }}</p>
                    <div v-if="swap.status === 'pending'" class="swap-actions">
                        <button @click="acceptSwap(swap.id)" class="btn btn-success">Accept</button>
                        <button @click="rejectSwap(swap.id)" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </div>
            <div v-else>
                <p class="no">No swap requests available.</p>
            </div>
        </div>
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
                swaps: [],
                filter: 'all'
            };
        },
        computed: {
            filteredSwaps() {
                if (this.filter === 'all') {
                    return this.swaps;
                }
                return this.swaps.filter(swap => swap.status === this.filter);
            }
        },
        mounted() {
            this.fetchSwaps();
        },
        methods: {
            fetchSwaps() {
                axios.get('/api/swaps')
                    .then(response => {
                        this.swaps = response.data;
                    })
                    .catch(error => {
                        console.error("Error fetching swaps:", error);
                    });
            },
            acceptSwap(swapId) {
                axios.post(`/api/swaps/${swapId}/accept`)
                    .then(response => {
                        alert(response.data.message);
                        this.fetchSwaps(); // Refresh the swaps list
                    })
                    .catch(error => {
                        console.error("Error accepting swap:", error);
                    });
            },
            rejectSwap(swapId) {
                axios.post(`/api/swaps/${swapId}/reject`)
                    .then(response => {
                        alert(response.data.message);
                        this.fetchSwaps(); // Refresh the swaps list
                    })
                    .catch(error => {
                        console.error("Error rejecting swap:", error);
                    });
            }
        }
    };
</script>

<style scoped>
    .swap-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        margin-bottom: 250px;
    }

    .no {
        margin-bottom: 99px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .filters {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .filters button {
        padding: 10px 20px;
        margin: 0 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .filters button.active {
        background-color: #007bff;
        color: white;
    }

    .swap-request {
        border: 1px solid #ddd;
        padding: 20px;
        margin: 10px 0;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .swap-actions {
        margin-top: 10px;
    }

    .swap-actions .btn {
        padding: 10px 20px;
        margin-right: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
</style>