import React from 'react';
import {connect} from "react-redux";
import {compose} from "redux";
import DashboardPage from "./DashboardPage";
import AuthPage from "./AuthPage";

class HomePage extends React.Component {
    render() {
        const {user} = this.props;
        return (
            <div className="w-100 h-100">
                {user ? <DashboardPage {...this.props}/> : <AuthPage {...this.props}/>}
            </div>
        );
    }
}

const mapStateToProps = state => ({
    user: state.appState.user
});

export default compose(connect(mapStateToProps))(HomePage);
