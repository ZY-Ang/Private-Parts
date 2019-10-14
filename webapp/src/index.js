import React from 'react';
import ReactDOM from 'react-dom';
import './Facebook';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.bundle.js';
import App from './App';
import {Provider} from 'react-redux';
import store from './redux';
import * as serviceWorker from './serviceWorker';
import firebase from "firebase/app";
import 'firebase/auth';

firebase.initializeApp({
	apiKey: "AIzaSyBXmpag_PiuvkNQIjb2mgv_ZpO9FDferLo",
	authDomain: "private-parts.firebaseapp.com",
	databaseURL: "https://private-parts.firebaseio.com",
	projectId: "private-parts",
	storageBucket: "private-parts.appspot.com",
	messagingSenderId: "519956892611",
	appId: "1:519956892611:web:b05bb205ee55a39d3a4b76"
});

ReactDOM.render(
    <Provider store={store}>
        <App/>
    </Provider>,
document.getElementById('root'));

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
