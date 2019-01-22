import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { FilePond, File, registerPlugin } from 'react-filepond';
import 'filepond/dist/filepond.min.css';


class AddVideo extends Component {

    constructor(props) {
        super(props);
        this.state = {
            language: 0,
            title: '',
            description: '',
            files: [],
            errors: []
        }
    }

    render() {
        return(
            <div className="container">
                <h1>Upload video</h1>
                <FilePond className="margin-top-20" 
                    server={
                        {
                            process: {
                                url: '/api/videos/upload',                            
                            },
                            revert: {
                                url: '/api/videos/delete',
                                method: 'POST',
                            },
                        }
                    } 
                    onupdatefiles={(fileItems) => {
                        this.setState({
                            files: fileItems.map(fileItem => fileItem.file)
                        });
                    }}>
                        {this.state.files.map(file => (
                            <File key={file} src={file} origin="local" />
                        ))}
                    </FilePond>                    
            </div>
        )
    }
}

export default AddVideo;