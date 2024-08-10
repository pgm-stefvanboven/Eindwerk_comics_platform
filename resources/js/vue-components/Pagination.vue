<template>
    <div class="pagination">
        <button @click="previousPage" :disabled="current === 1">Previous</button>
        <span>Page {{ current }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="current === totalPages">Next</button>
    </div>
</template>

<script>
    export default {
        props: {
            total: {
                type: Number,
                required: true
            },
            current: {
                type: Number,
                required: true
            },
            perPage: {
                type: Number,
                default: 8 // Set default items per page to 8
            }
        },
        computed: {
            totalPages() {
                return Math.ceil(this.total / this.perPage);
            }
        },
        methods: {
            previousPage() {
                if (this.current > 1) {
                    this.$emit('page-changed', this.current - 1);
                }
            },
            nextPage() {
                if (this.current < this.totalPages) {
                    this.$emit('page-changed', this.current + 1);
                }
            }
        }
    }
</script>

<style scoped>
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        margin-bottom: 40px;
    }

    .pagination button {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        margin: 0 5px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .pagination button:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .pagination button:not(:disabled):hover {
        background: #0056b3;
    }

    .pagination span {
        margin: 0 10px;
        color: #333;
        font-size: 1rem;
    }
</style>