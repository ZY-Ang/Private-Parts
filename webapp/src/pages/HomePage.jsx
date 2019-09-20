import React from 'react';
import logo from '../logo512.png';
import {connect} from "react-redux";
import {compose} from "redux";

class HomePage extends React.Component {
    authenticateFacebook = () => {
        // TODO
    };

    authenticateTwitter = () => {
        // TODO
    };

    render() {
        return (
            <div>
                <img
                    alt="Private Parts"
                    src={logo}
                    width={200}
                    height={200}
                />
                <div>
                    Home
                </div>
            </div>
        );
    }
}

const mapStateToProps = state => ({
    user: state.appState.user
});

const mapDispatchToProps = dispatch => ({
    setUser: (user) => dispatch({type: 'setUser', user})
});

export default compose(connect(mapStateToProps, mapDispatchToProps))(HomePage);
