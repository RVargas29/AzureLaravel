import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route, Switch } from 'react-router-dom';

import FEHeader from './Layout/FEHeader';
import AddVideo from './Videos/AddVideo';

export default class App extends Component {
    render() {
        return (
            <BrowserRouter>
                <div>
                    <FEHeader />
                    <Switch>
                        <Route exact path='/' component={AddVideo}/>
                    </Switch>
                </div>
            </BrowserRouter>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
