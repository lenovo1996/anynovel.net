const routes = {
    read: "getBook",
};

$(document).ready(function () {
    let path = Controller.getPath();

    let functionName = routes[path[1]];

    if (typeof routes[path[1]] == "undefined") {
        window.location.href = window.location.origin;
    }

    Controller[functionName]();
});

let Controller = {
    getBook() {
        Controller.loadInfo();
        Controller.loadContent();
        Controller.loadChapterList();
    },


    loadInfo() {
        let route = Controller.getPath();

        let book_id = route[2];

        $.getJSON('/storage/vi/' + book_id + '/detail.json')
            .then(function (res) {
                $('.book-name').text(res.data.book_name);
            });
    },

    loadContent() {
        let route = Controller.getPath();

        let book_id = route[2];
        let section_id = route[3];

        $.getJSON('/storage/vi/' + book_id + '/sections/' + section_id + '/detail.json')
            .then(function (data) {
                $('.chapter_title').text(data.title);
            });

        $.get('/storage/vi/' + book_id + '/sections/' + section_id + '/content.txt')
            .then(function (data) {
                $('.chapter-content').html(data);
            });
    },

    loadChapterList() {

    },

    getPath() {
        let path = window.location.pathname;
        return path.split("/");
    }
};
