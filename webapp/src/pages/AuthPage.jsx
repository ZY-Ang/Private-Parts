import React from 'react';
import FaIcon from "../components/FaIcon";
import AppRedux from "../redux";
import {byPropKey} from "../utils";
import firebase from "firebase/app";

// const FACEBOOK_SCOPES = ['public_profile', 'email'].sort().join(', ');

class AuthPage extends React.Component {
	state = {
		email: '',
		instagram: ''
	};

	signInWithEmail = e => {
		AppRedux.dispatch({type: 'setUser', user: {
			type: 'email',
			email: this.state.email
		}});
		if (e) e.preventDefault();
	};

	signInWithFacebook = () => {
		const provider = new firebase.auth.FacebookAuthProvider();
		provider.addScope('email');
		provider.addScope('public_profile');
		provider.setCustomParameters({
			'display': 'popup'
		});
		firebase.auth().signInWithPopup(provider)
			.then(res => {
				AppRedux.dispatch({type: 'setUser', user: {
					type: 'email',
					email: res.user.email
				}});
			});
		// Old facebook code - fb-sdk-wrapper
		// Facebook.getLoginStatus()
		// 	.then(({status, authResponse}) => {
		// 		if (status === 'connected') {
		// 			AppRedux.dispatch({type: 'setUser', user: {
		// 				...user,
		// 				facebook: authResponse
		// 			}});
		// 			return authResponse.userID;
		// 		} else {
		// 			return Facebook.login({scope: FACEBOOK_SCOPES, return_scopes: true})
		// 				.then(({status, authResponse}) => {
		// 					if (status === 'connected') {
		// 						AppRedux.dispatch({type: 'setUser', user: {
		// 							...user,
		// 							facebook: authResponse
		// 						}});
		// 						return authResponse.userID;
		// 					}
		// 				});
		// 		}
		// 	})
		// 	.then(userID => Facebook.api(`/${userID}/`))
		// 	.then(response => console.log({response}))
		// 	.catch(err => console.error(err));
	};

	signInWithInstagram = e => {
		AppRedux.dispatch({type: 'setUser', user: {
			type: 'instagram',
			instagram: this.state.instagram
		}});
		if (e) e.preventDefault();
	};

	render() {
		return (
			<div className="d-flex flex-column justify-content-center align-items-center h-100">
				<h5>Pick an option to proceed:</h5>
				<form
					className="input-group input-group-auth mt-3"
					onSubmit={this.signInWithEmail}
				>
					<input
						type="email"
						className="form-control"
						placeholder="Email Address"
						aria-label="Email Address"
						onChange={e => this.setState(byPropKey('email', e.target.value))}
						required
					/>
					<div className="input-group-append">
						<button className="btn btn-outline-secondary" type="submit">
							<FaIcon icon="chevron-right" className="first-chevron"/>
						</button>
					</div>
				</form>
				<div className="btn-login-facebook mt-3">
					<button
						className="btn-auth-social"
						onClick={this.signInWithFacebook}
					>
						<div className="btn-auth-social-icon">
							<FaIcon icon={['fab', 'facebook-f']}/>
						</div>
						<div className="btn-auth-text btn-auth-social-text">Sign in With Facebook</div>
					</button>
				</div>
				<form
					className="input-group input-group-auth mt-3"
					onSubmit={this.signInWithInstagram}
				>
					<div className="input-group-prepend">
						<span className="input-group-text" id="insta-addon">@</span>
					</div>
					<input
						type="text"
						className="form-control"
						placeholder="Instagram Username"
						aria-label="Instagram Username"
						aria-describedby="insta-addon"
						onChange={e => this.setState(byPropKey('instagram', e.target.value))}
						required
					/>
					<div className="input-group-append">
						<button className="btn btn-outline-secondary" type="submit">
							<FaIcon icon="chevron-right" className="first-chevron"/>
						</button>
					</div>
				</form>
			</div>
		);
	}
}

export default AuthPage;
