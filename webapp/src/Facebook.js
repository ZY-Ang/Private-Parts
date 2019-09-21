import * as Facebook from 'fb-sdk-wrapper';

Facebook.load()
	.then(() => {
		Facebook.init({
			appId      : '1245739062294998',
			cookie     : true,
			xfbml      : true,
			version    : 'v4.0'
		});
	});

export default Facebook;
