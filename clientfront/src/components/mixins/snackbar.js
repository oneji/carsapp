const snackbar = {
    data() {
        return {
            snackbar: {
                active: false,
                text: '',
                timeout: 5000,
                color: ''
            },
        }
    },

    methods: {
        successSnackbar(message) {
            this.snackbar.color = 'success';
            this.snackbar.text = message;
            this.snackbar.active = true;
        },

        errorSnackbar(message) {
            this.snackbar.color = 'error';
            this.snackbar.text = message;
            this.snackbar.active = true;
        },

        warningSnackbar(message) {
            this.snackbar.color = 'warning';
            this.snackbar.text = message;
            this.snackbar.active = true;
        }
    }
}

export default snackbar