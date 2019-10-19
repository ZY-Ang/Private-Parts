const axios = require('axios');
/**
 * Have I Been Pwned API Proxy
 */
module.exports = (req, res) => {
    const hibp = axios.create({
        baseURL: 'https://haveibeenpwned.com/api/v3/',
        headers: {
            'hibp-api-key': '2c73716c824b40fd9382cdbadbba3ddc',
            'user-agent': 'private-parts'
        }
    });
    hibp.get(`/${req.params[0]}`)
        .then(({status, data}) => res.status(status).send(data))
        .catch(err => {
            if (err.response) {
                res.status(err.response.status).send(err.response.data);
            } else if (err.request) {
                console.error(err.request);
                res.sendStatus(500);
            } else {
                console.error("Error", err.message);
                res.sendStatus(500);
            }
        });
};
