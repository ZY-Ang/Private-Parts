import React from 'react';
import {breachedAccount, } from "hibp";

class DashboardPage extends React.Component {
	state = {};

	componentDidMount() {
		const {user} = this.props;
		console.log({user});

		breachedAccount(user.email, {
			apiKey: "2c73716c824b40fd9382cdbadbba3ddc"
		})
			.then(console.log)
			.catch(console.error);
	}

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
