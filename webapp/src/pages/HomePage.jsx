import React from 'react';
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
