const routes = {
    read: "getBook",
};

$(document).ready(function () {
    let path = Controller.getPath();

    let functionName = routes[path[1]];

    if (typeof routes[path[1]] == "undefined") {
        return;
        window.location.href = window.location.origin;
        return;
    }

    Controller[functionName]();
});

$(document).on("click", '.chapter-list-btn', function () {
    if ($('.chapter-list').is(':hidden')) {
        $('.chapter-list').fadeIn();
    } else {
        $('.chapter-list').fadeOut();
    }
});

let Controller = {
    getBook() {
        Controller.loadInfo();
        Controller.loadChapterList();
    },


    loadInfo() {
        let route = Controller.getPath();

        let book_id = route[2];

        $.getJSON('/storage/vi/' + book_id + '/detail.json')
            .then(function (res) {
                $('.book-name').text(res.data.book_name);
                document.title = res.data.book_name;
            });
    },

    loadContent(param_id) {
        let route = Controller.getPath();
        let book_id = route[2];
        let section_id = route[3];

        if (param_id) {
            section_id = param_id;
        }

        $.get('/storage/vi/' + book_id + '/sections/' + section_id + '/content.txt')
            .then(function (data) {
                $('.chapter_content').html(data);
            });
    },

    loadChapterList() {
        let route = Controller.getPath();
        let book_id = route[2];
        let section_id = route[3];

        $.getJSON('/storage/vi/' + book_id + '/sections.json')
            .then(function (data) {
                if (!section_id) {
                    section_id = data[0].section_id;
                }

                Controller.loadContent(section_id);

                let html = '';
                $.each(data, function () {
                    html += `<div class="chapter section-${this.section_id}"><a href="/read/${book_id}/${this.section_id}">${this.title}</a></div>`;
                });
                $('.chapter-list').html(html);
                $(`.section-${section_id}`).addClass('active');
            });
    },

    getPath() {
        let path = window.location.pathname;
        return path.split("/");
    }
};
