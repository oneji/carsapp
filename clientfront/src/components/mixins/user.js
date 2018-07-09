const user = {
    computed: {
        user() {
            return window.localStorage.getItem('user') !== undefined ? JSON.parse(window.localStorage.getItem('user')) : {};
        }
    }
}

export default user