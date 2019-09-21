import React from 'react';

class DashboardPage extends React.Component {
	state = {
		fbProfile: null
	};

	componentDidMount() {
		const {user} = this.props;
		const component = this;
		console.log({user});
		const initMap = {
			email() {
				return component.initEmail();
			},
			facebook() {
				return component.initFacebook();
			},
			twitter() {
				return component.initTwitter();
			},
			instagram() {
				return component.initInstagram();
			}
		};
		if (!!initMap[user.type]) {
			initMap[user.type]();
		}
	}

	initEmail = () => {

	};

	initFacebook = () => {
		// const {facebook} = this.props.user;
		// Name TODO

		// Profile pic
		// await Facebook.api(`/me?fields=id,name,birthday,email`)
		// 	.then(res => {
		// 		console.log({res});
		// 		this.setState({fbProfile: res.data});
		// 	})
		// 	.catch(console.error);
	};

	initTwitter = () => {

	};

	initInstagram = () => {

	};

	render() {
		const {user} = this.props;
		return(
			<div className="h-100 d-flex flex-column align-items-center justify-content-center text-center">
				<div className="alert alert-danger" role="alert">
					This should be a dashboard of the stuff to show the user. Not sure what we have yet so this is blank.
				</div>
				{
					user.type === 'email' &&
					<div>
						<p>Your email address:</p>
						<code>{user.email}</code>
					</div>
				}
				{
					user.type === 'facebook' &&
					<div>
						<p>Your facebook access token:</p>
						<code>{user.facebook.accessToken}</code>
						<p>Other information may be available in stdout</p>
					</div>
				}
				{
					user.type === 'twitter' &&
					<div>
						<p>Work in progress</p>
					</div>
				}
				{
					user.type === 'instagram' &&
					<div>
						<p>Your instagram username:</p>
						<code>{user.instagram}</code>
					</div>
				}
			</div>
		);
	}
}

export default DashboardPage;
