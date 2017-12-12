import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import ReactPaginate from 'react-paginate';
import $ from 'jquery';

window.React = React;


export class TrackList extends Component {
    render() {
        let trackNodes = this.props.data.map(function(track, index) {
            return (
                <div key={index} class="row">
                    <div className="col-md-3">{track.id}</div>
                    <div className="col-md-3">{track.title}</div>
                    <div className="col-md-3">{track.duration}</div>
                    <div className="col-md-3"> <a href={track.deleteUrl}><i className="glyphicon glyphicon-remove"/></a></div>
                </div>
            );
        });

        return (
            <div id="project-tracks" className="TrackList">
                {trackNodes}
            </div>
      );
    }
};

export class App extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: [],
            offset: 0
        }
    }

    loadTracksFromServer() {
        $.ajax({
                url      : this.props.url,
                data     : {numberPerPage: this.props.perPage, offset: this.state.offset},
                dataType : 'json',
                type     : 'GET',

                success: data => {
                this.setState({data: data.tracks, pageCount: Math.ceil(data.meta.total_count / data.meta.numberPerPage)});
    },

        error: (xhr, status, err) => {
            console.error(this.props.url, status, err.toString());
        }
    });
    }

    componentDidMount() {
        this.loadTracksFromServer();
    }



    handlePageClick = (data) => {
        let selected = data.selected;
        let offset = Math.ceil(selected * this.props.perPage);

        this.setState({offset: offset}, () => {
            this.loadTracksFromServer();
        });
    };


render() {
    return (
        <div className="trackBox">
                <TrackList data={this.state.data} />
                <ReactPaginate previousLabel={"previous"}
                    nextLabel={"next"}
                    pageCount={this.state.pageCount}
                    marginPagesDisplayed={2}
                    pageRangeDisplayed={5}
                    onPageChange={this.handlePageClick}
                    containerClassName={"pagination"}
                    subContainerClassName={"pages pagination"}
                    activeClassName={"active"} />

        </div>

        );
    }
};

ReactDOM.render(
<App url={'http://localhost/panorabanquedeezer/public/index.php/tracksJson'}
        perPage={10} />,
        document.getElementById('react-paginate')
);