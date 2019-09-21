import React from 'react';
import {Link} from "react-router-dom";
import {ROUTE_ABOUT, ROUTE_HOME, ROUTE_SIGN_OUT} from "../constants";
import logo from "../logo512.png";
import {connect} from "react-redux";
import {compose} from "redux";

class Navbar extends React.Component {
    render() {
        const {location, user} = this.props;
        return (
            <nav className="navbar sticky-top navbar-expand-sm navbar-light bg-light" style={{minHeight: 76}}>
                <Link className="navbar-brand" to={ROUTE_HOME}>
                    <img
                        alt="Private Parts"
                        src={logo}
                        width={50}
                        height={50}
                    />
                    <span className="ml-2">Private Parts</span>
                </Link>
                <button className="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"/>
                </button>
                <div className="collapse navbar-collapse bg-light" id="navbarNavAltMarkup">
                    <div className="navbar-nav mr-auto">
                        <Link className={`nav-item nav-link${location === ROUTE_HOME ? ' active' : ''}`} to={ROUTE_HOME}>Home</Link>
                        <Link className={`nav-item nav-link${location === ROUTE_ABOUT ? ' active' : ''}`} to={ROUTE_ABOUT}>About</Link>
                    </div>
                    {
                        user &&
                        <div className="navbar-nav">
                            <Link className={`nav-item nav-link${location === ROUTE_HOME ? ' active' : ''}`} to={ROUTE_SIGN_OUT}>Sign Out</Link>
                        </div>
                    }
                </div>
            </nav>
        );
    }
}

const mapStateToProps = state => ({
    user: state.appState.user
});

export default compose(connect(mapStateToProps))(Navbar);
