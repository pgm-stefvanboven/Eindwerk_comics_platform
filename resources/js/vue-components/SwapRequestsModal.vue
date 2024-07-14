<template>
    <div class="modal">
        <div class="modal-content">
            <span class="close" @click="$emit('close')">&times;</span>
            <h2>Swap Requests</h2>
            <div v-if="requests.length">
                <div v-for="request in requests" :key="request.id" class="swap-request">
                    <p><strong>{{ request.user.name }}</strong> wants to swap their <strong>{{ request.comic.title
                            }}</strong> for your <strong>{{ request.requestedComic.title }}</strong></p>
                    <button @click="acceptSwap(request)" class="btn btn-success">Accept</button>
                    <button @click="rejectSwap(request)" class="btn btn-danger">Reject</button>
                </div>
            </div>
            <div v-else>
                <p>No swap requests at the moment.</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['requests'],
        methods: {
            acceptSwap(request) {
                axios.post(`/api/swaps/${request.id}/accept`)
                    .then(response => {
                        this.$emit('update');
                    })
                    .catch(error => {
                        console.error("Error accepting swap:", error);
                    });
            },
            rejectSwap(request) {
                axios.post(`/api/swaps/${request.id}/reject`)
                    .then(response => {
                        this.$emit('update');
                    })
                    .catch(error => {
                        console.error("Error rejecting swap:", error);
                    });
            }
        }
    }
</script>

<style scoped>
    .modal {
        display: block;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
        position: relative;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .swap-request {
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }

    .swap-request:last-child {
        border-bottom: none;
    }
</style>