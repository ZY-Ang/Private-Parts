import React from 'react';
import Facebook from '../Facebook';
import {withRouter} from 'react-router-dom';
import AppRedux from "../redux";
import {ROUTE_HOME} from "../constants";

class SignOutPage extends React.Component {
    state = {
        redirectIn: 3,
        timeouts: []
    };

    componentDidMount() {
        Facebook.logout().catch(console.warn);
        AppRedux.dispatch({type: 'signOut'});
        window.localStorage.clear();
        this.setState({
            timeouts: [
                setTimeout(() => this.setState({redirectIn: 2}), 1000),
                setTimeout(() => this.setState({redirectIn: 1}), 2000),
                setTimeout(this.redirectToHome, 3000)
            ]
        });
    }

    redirectToHome = async () => {
        await new Promise(resolve => this.setState({redirectIn: 0}, resolve));
        if (!this.props.history) {
            document.location.href = ROUTE_HOME;
        } else {
            this.props.history.push(ROUTE_HOME);
        }
    };

    componentWillUnmount() {
        this.state.timeouts.forEach(clearTimeout);
    }

    render() {
        return (
            <div className="w-100 h-100">
                <div className="container text-center d-flex flex-column align-items-center justify-content-center h-100">
                    <div>
                        <h1>Signing you out</h1>
                        <p>Clearing local storage and application state...</p>
                        <p>You will be redirected to the home page in {this.state.redirectIn} seconds.</p>
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(SignOutPage);
