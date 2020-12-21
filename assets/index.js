$(document).ready(function () {
    $.getJSON('/storage/vi/book_list.json').then(function (json) {
        $('.book-list').html('');
        $.each(json, function () {
            parseJsonToTemplate(this);
        });
    });
});

$(document).on('input', '.input-search', function () {
    let value = $('.input-search').val();
    $('.book-detail').hide();

    $.each($('.book-detail'), function () {
        let title = $(this).attr('data-title');
        if (title.toLowerCase().includes(value.toLowerCase())) {
            $(this).show()
        }
    })
});

function parseJsonToTemplate(book) {
    let book_label = '';
    $.each(book.label, function () {
        book_label += `<div class="label">${this}</div>`;
    });

    let html = `<div class="book-detail" data-title="${book.title}">
                <div class="thumbnail left">
                    <img src="${book.image}">
                </div>
                <div class="detail right">
                    <div class="book-info book-info-name">
                        <a href="/read/${book.book_id}" title="${book.title}">${book.title}</a>
                    </div>
                    <div class="book-info book-info-section">
                        <span class="last-section">
                            <a href="/read/${book.book_id}/${book.section.section_id}" title="${book.section.title}">${book.section.title}</a>
                        </span>
                    </div>
                    <div class="book-info book-info-update-time">
                        <span class="update-time">Updated: ${book.section_update_time}</span>
                    </div>
                    <div class="book-info-author">
                        <div class="tag author">${book.author}</div>
                        <div class="tag category">${book.category}</div>
                        <div class="tag score">${book.score}</div>
                    </div>
                    <div class="book-info-label">${book_label}</div>
                </div>
                <div class="clearfix"></div>
            </div>`;
    $('.book-list').append(html);
}

