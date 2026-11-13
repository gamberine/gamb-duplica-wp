jQuery(document).ready(function ($) {
    if (typeof gamb-duplica-wp === 'undefined') {
        gamb-duplica-wp = {};
        gamb-duplica-wp.Help = {};
    }

    gamb-duplica-wp.Help.Data = null;

    gamb-duplica-wp.Help.isDataLoaded = function() {
        return gamb-duplica-wp.Help.Data !== null;
    };

    gamb-duplica-wp.Help.ToggleCategory = function(categoryHeader) {
        $(categoryHeader).find(".fa-angle-right").toggleClass("fa-rotate-90");
        $(categoryHeader).siblings(".gamb-duplica-wp-help-article-list").slideToggle();
        $(categoryHeader).siblings(".gamb-duplica-wp-help-category-list").slideToggle();
    };

    gamb-duplica-wp.Help.Search = function(search) {
        let results   = $("#gamb-duplica-wp-help-search-results");
        let noResults = $("#gamb-duplica-wp-help-search-results-empty");
        let context   = $("#gamb-duplica-wp-context-articles");
        let articles  = $(".gamb-duplica-wp-help-article");
        let regex     = gamb-duplica-wp.Help.GetRegex(search);

        if (search.length === 0 && regex === null) {
            context.show();
            results.hide();
            noResults.hide();
            return;
        }

        let found    = false;
        let foundIds = [];

        context.hide();
        results.empty();

        articles.each(function() {
            let article = $(this);
            let id      = article.data("id");
            let title   = article.find("a").text().toLowerCase();

            if (title.search(regex) !== -1 && foundIds.indexOf(id) === -1) {
                found = true;
                results.append(article.clone());
                foundIds.push(id);
            }
        });

        if (found) {
            results.show();
            noResults.hide();
        } else {
            results.hide();
            noResults.show();
        }
    };

    gamb-duplica-wp.Help.Load = function(url) {
        if (gamb-duplica-wp.Help.isDataLoaded()) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function(xhr) {
                gamb-duplica-wp.Util.ajaxProgressShow();
            },
            success: function (result) {
                gamb-duplica-wp.Help.Data = result;
                //because ajax is async we need to open the modal here for first time
                gamb-duplica-wp.Help.Display();
            },
            error: function (result) {
                DupPro.addAdminMessage(l10nDupDynamicHelp.failedLoad, 'error');
            },
            complete: function () {
                gamb-duplica-wp.Util.ajaxProgressHide();
            },
        });
    };

    gamb-duplica-wp.Help.Display = function() {
        if (!gamb-duplica-wp.Help.isDataLoaded()) {
            throw 'gamb-duplica-wp.Help.Data is null';
        }

        let box = new gamb-duplica-wpModalBox({
            htmlContent: gamb-duplica-wp.Help.Data,
        });
        box.open();
    };

    gamb-duplica-wp.Help.GetRegex = function(search = '') {
        let regexStr = '';
        let regex = null;

        if (search.length < 1) {
            return null;
        }

        $.each(search.split(' '), function(key, value) {
            //escape regex
            value = value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            if (value.length > 1) {
                regexStr += '(?=.*'+value+')';
            }
        });

        regex = new RegExp(regexStr, 'i');
        return regex;
    };


    $("body").on("click", ".gamb-duplica-wp-help-category header", function() {
        gamb-duplica-wp.Help.ToggleCategory(this);
    });

    $("body").on("keyup", "#gamb-duplica-wp-help-search input", function() {
        gamb-duplica-wp.Help.Search($(this).val().toLowerCase());
    });
});
