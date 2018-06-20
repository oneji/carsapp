export default {
    apiURL: process.env.NODE_ENV === 'production' ? 'http://api.cars.55soft.net/api/' : 'http://55cars.loc/api/',
    assetsURL: process.env.NODE_ENV === 'production' ? 'http://api.cars.55soft.net' : 'http://55cars.loc'
}