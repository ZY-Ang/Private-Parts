import React from 'react';
import {Link} from "react-router-dom";
import {ROUTE_PRIVACY_POLICY, ROUTE_TERMS} from "../constants";
import logo from "../logo512.png";
import {getCurrentYear} from "../utils";

class Footer extends React.Component {
    render() {
        return (
            <footer className="footer bg-light">
                <div className="container text-center">
                    <Link className="mr-2" to={ROUTE_TERMS}>Terms & Conditions</Link>
                    <Link className="ml-2" to={ROUTE_PRIVACY_POLICY}>Privacy Policy</Link>
                    <div className="row">
                        <div className="col-md">
                            <img
                                alt="Private Parts"
                                src={logo}
                                width={24}
                                height={24}
                            />
                            <span className="ml-1">Private Parts | A CS3235 White Hat Tool</span>
                        </div>
                        <div className="col-md">
                            <span><small>&copy; Copyright {getCurrentYear()} National University of Singapore</small></span>
                        </div>
                    </div>
                </div>
            </footer>
        );
    }
}

export default Footer;
