const generateActNum = {
    filters: {
        generateActNum(value) {
            return (value / 10000).toString().replace('.', '');
        }
    },
}

export default generateActNum