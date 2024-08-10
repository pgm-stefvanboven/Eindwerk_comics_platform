<template>
    <div>
        <Header />
        <section class="collection">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>My Collection</h1>
                        <p class="collection-intro-text">
                            Welcome to your own collection page! Here you can see the comics you rented or bought, rate
                            them and search for specific comics. Enjoy! 🦸‍♂
                        </p>
                    </div>
                </div>
            </div>
        </section>
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
    }
</script>

<style scoped>
    section.collection {
        padding: 30px 0;
        max-width: 1536px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .collection h1 {
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .collection-intro-text {
        max-width: 600px;
        font-size: 1.25rem;
        text-align: center;
        margin: 0 auto 20px;
        margin-bottom: 20px;
        color: #666;
    }
</style>