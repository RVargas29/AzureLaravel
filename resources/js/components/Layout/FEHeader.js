import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class FEHeader extends Component {
    render() {
        return(
            <nav className="play-navbar">
                <a href="#" target="_blank">
                    <img src="/img/logos/iica.svg" alt="IICAPlay" />
                </a>
                <ul className="menu">
                    <li>
                        <a href="#home"><span className="glyphicon glyphicon-home"></span> Home</a>
                    </li>
                    <li>
                        <a href="#search">Buscar</a>
                    </li>
                    <li>
                        <a href="#autentication" className="btn btn-iica-green"><span className="glyphicon glyphicon-lock"></span> Autenticación</a>
                    </li>
                </ul>
            </nav>
        );        
    }
}

export default FEHeader;