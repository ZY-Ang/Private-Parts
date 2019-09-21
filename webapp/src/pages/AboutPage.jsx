import React from 'react';

class AboutPage extends React.Component {
    render() {
        return (
            <div className="w-100">
                <div className="container mt-5">
                    <div className="alert alert-warning" role="alert">
                        TODO: Add more description here for the module. More FAQs, etc.
                    </div>
                    <h1>About Private Parts</h1>
                    <p>Private Parts is a security tool to help you find the parts of your private information are being leaked online.</p>
                    <p>This web application allows you to find out how much stalkers can find out about you and identifies vulnerabilities in your public profile which you might want to keep private.</p>
                    <h3>FAQ</h3>
                    <div>
                        <p><b>Does this web application keep any of my information?</b></p>
                        <p>No.</p>
                    </div>
                    <div>
                        <p><b>Is this a commercial project?</b></p>
                        <p>This is a strictly academic project for identifying privacy vulnerabilities.</p>
                    </div>
                </div>
            </div>
        );
    }
}

export default AboutPage;
