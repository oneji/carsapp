import config from '@/config'

const assetsURL = {
    computed: {
        assetsURL() {
            return config.assetsURL;
        }
    }
}

export default assetsURL