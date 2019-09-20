import React from 'react';
import {Link} from "react-router-dom";
import {ROUTE_PRIVACY_POLICY, ROUTE_TERMS} from "../constants";

class Footer extends React.Component {
    render() {
        return (
            <div>
                Footer
                <div>
                    <Link to={ROUTE_TERMS}>Terms & Conditions</Link>
                    <Link to={ROUTE_PRIVACY_POLICY}>Privacy Policy</Link>
                </div>
            </div>
        );
    }
}

export default Footer;
