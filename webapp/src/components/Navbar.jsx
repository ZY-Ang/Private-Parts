import React from 'react';
import {Link} from "react-router-dom";
import {ROUTE_HOME, ROUTE_SIGN_OUT} from "../constants";

class Navbar extends React.Component {
    render() {
        return (
            <div className="navbar">
                <ul className="nav navbar-nav navbar-right">
                    <li role="presentation" className="nav-item active"><Link to={ROUTE_HOME}>Home</Link></li>
                    <li role="presentation" className="nav-item active"><Link to={ROUTE_SIGN_OUT}>Sign Out</Link></li>
                </ul>
            </div>
        );
    }
}

export default Navbar;
