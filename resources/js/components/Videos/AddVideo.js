import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { FilePond } from 'react-filepond';
import 'filepond/dist/filepond.min.css';


class AddVideo extends Component {
    render() {
        return(
            <div className="container">
                <h1>Upload video</h1>
                <FilePond className="margin-top-20" server="./api/videos/upload"></FilePond>
            </div>
        )
    }
}

export default AddVideo;