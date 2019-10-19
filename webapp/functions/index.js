const functions = require('firebase-functions');
const admin = require('firebase-admin');
const express = require('express');
const cors = require('cors');
admin.initializeApp();

const app = express();

app.use(cors({origin: true}));

app.all('/hibp/*', require('./hibp'));
app.all('/', (req, res) => res.sendStatus(404));

exports.api = functions.runWith({timeoutSeconds: 540}).region('asia-east2').https.onRequest(app);
