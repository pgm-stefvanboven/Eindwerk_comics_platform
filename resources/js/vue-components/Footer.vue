<template>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p class="marvel-text">Data provided by Marvel. © 2024 Marvel</p>
                <p class="comicverse-text">© 2024 ComicVerse</p>
                <div class="footer-links">
                    <ul class="link-list">
                        <li><a href="/characters">Characters</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
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
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 70px;
}

.footer-content {
    display: flex;
    flex-direction: row;
    width: 100%;
    justify-content: space-between;
    align-items: center;
}

.footer-text {
    display: flex;
    flex-direction: row;
    width: 100%;
    justify-content: space-between;
    align-items: center;
}

.marvel-text {
    max-width: 16%;
    text-align: center;
}

.marvel-text,
.comicverse-text {
    font-size: 20px;
    font-weight: 400;
    margin-right: 20px;
    margin-left: 5px;
}

.link-list {
    list-style-type: none;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    margin: 0;
    padding: 0;
}

.link-list li {
    margin-right: 20px;
}

.link-list a {
    text-decoration: none;
    color: white;
    font-size: 20px;
    font-weight: 400;
}
</style>