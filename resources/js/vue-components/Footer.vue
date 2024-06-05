<template>
    <footer class="footer">
        <div class="container footer-content">
            <div class="marvel-text">Data provided by Marvel. © 2024 Marvel</div>
            <div class="comicverse-text">© 2024 ComicVerse</div>
            <div class="footer-links">
                <ul class="link-list">
                    <li><a href="/characters">Characters</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </footer>
</template>

<script>
    export default {
        name: 'Footer'
    };
</script>

<style scoped>
    .footer {
        background-color: #FF6A00;
        color: white;
        padding: 20px 0;
        margin-top: 70px;
    }

    .container {
        max-width: 1536px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: center;
    }

    .marvel-text {
        max-width: 230px;
        font-size: 20px;
        font-weight: 400;
        text-align: center;
        flex: 1;
    }

    .comicverse-text {
        font-size: 20px;
        font-weight: 400;
        text-align: center;
        flex: 3;
    }

    .footer-links {
        text-align: right;
        flex: 1;
    }

    .link-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: flex-end;
    }

    .link-list li {
        margin: 0 10px;
    }

    .link-list a {
        text-decoration: none;
        color: white;
        font-size: 20px;
        font-weight: 400;
    }

    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
        }

        .marvel-text,
        .comicverse-text,
        .footer-links {
            flex: none;
            margin: 10px 0;
        }

        .link-list {
            justify-content: center;
        }
    }
</style>