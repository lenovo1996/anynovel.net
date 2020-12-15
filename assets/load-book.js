const routes = {
  read: "getBook",
};

$(document).ready(function () {
  let path = Controller.getPath();

  let functionName = routes[path[1]];

  if (typeof routes[pathSplit[1]] == "undefined") {
    window.location.href = window.location.origin;
  }

  Controller[functionName]();
});

let Controller = {
  getBook() {
    Controller.loadInfo();
    Controller.loadContent();
    Controller.loadChapter();
  },


  loadInfo() {
    let route = Controller.getPath();

    let book_id = route[2];

    $.get('./storage/vi/' + book_id + '/detail.json')
    .then(function (data) {
      
    });
  },

  loadContent() {
    let route = Controller.getPath();

    let book_id = route[2];
    let book_id = route[3];

    $.get('./storage/vi/' + book_id + '/sections/'+section_id+'/detail.json')
    .then(function (data) {
      
    });

    $.get('./storage/vi/' + book_id + '/sections/'+section_id+'/content.txt')
    .then(function (data) {
      $('.chapter-content').html(data);
    });
  },

  getPath() {
    let path = window.location.pathname;
    return path.split("/");
  }
};
