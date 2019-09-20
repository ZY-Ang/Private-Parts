import {combineReducers, createStore} from 'redux';

const INITIAL_STATE = {
    user: null
};

const appReducer = (state = INITIAL_STATE, action) => {
    const reducerMap = {
        setUser(){
            return {
                ...state,
                user: action.user
            };
        },
        signOut() {
            return INITIAL_STATE;
        }
    };
    if (!!action.type && !!reducerMap[action.type]) {
        return reducerMap[action.type]();
    } else {
        return state;
    }
};

const rootReducer = combineReducers({
    appState: appReducer
});
const AppRedux = createStore(rootReducer);
export default AppRedux;
