import React from 'react';
import './styles.css';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import {CSSTransition, TransitionGroup} from "react-transition-group";
import Navbar from "./components/Navbar";
import ScrollToTop from "./components/ScrollToTop";
import {
    ROUTE_HOME,
    ROUTE_NOT_FOUND,
    ROUTE_PRIVACY_POLICY,
    ROUTE_SIGN_OUT,
    ROUTE_TERMS,
    ROUTE_WILDCARD
} from "./constants";
import HomePage from "./pages/HomePage";
import NotFoundPage from "./pages/NotFoundPage";
import PrivacyPolicyPage from "./pages/PrivacyPolicyPage";
import Footer from "./components/Footer";
import SignOutPage from "./pages/SignOutPage";
import TermsPage from "./pages/TermsPage";

class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        return (
            <Router>
                <Route
                    render={({location}) =>
                        <div style={{height: '100%'}}>
                            <ScrollToTop>
                                <Navbar/>
                                <TransitionGroup>
                                    <CSSTransition
                                        key={location.key}
                                        timeout={300}
                                        classNames="fade"
                                    >
                                        <Switch location={location}>
                                            <Route path={ROUTE_HOME} exact component={HomePage} />
                                            <Route path={ROUTE_PRIVACY_POLICY} exact component={PrivacyPolicyPage} />
                                            <Route path={ROUTE_TERMS} exact component={TermsPage} />
                                            <Route path={ROUTE_SIGN_OUT} exact component={SignOutPage} />
                                            <Route path={ROUTE_NOT_FOUND} component={NotFoundPage} />
                                            <Route path={ROUTE_WILDCARD} component={NotFoundPage} />
                                        </Switch>
                                    </CSSTransition>
                                </TransitionGroup>
                                <Footer/>
                            </ScrollToTop>
                        </div>
                    }
                />
            </Router>
        );
    }
}

export default App;
