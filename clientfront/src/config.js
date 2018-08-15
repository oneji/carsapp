export default {
    apiURL: process.env.NODE_ENV === 'production' ? '//api.cars.55soft.net/api/' : '//55cars.loc/api/',
    assetsURL: process.env.NODE_ENV === 'production' ? '//api.cars.55soft.net' : '//55cars.loc'
}