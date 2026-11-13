jQuery(document).ready(function ($) {
    if (typeof GambDuplicaWP === 'undefined') {
        GambDuplicaWP = {};
        GambDuplicaWP.Help = {};
    }

    GambDuplicaWP.Help.Data = null;

    GambDuplicaWP.Help.isDataLoaded = function() {
        return GambDuplicaWP.Help.Data !== null;
    };

    GambDuplicaWP.Help.ToggleCategory = function(categoryHeader) {
        $(categoryHeader).find(".fa-angle-right").toggleClass("fa-rotate-90");
        $(categoryHeader).siblings(".gamb-duplica-wp-help-article-list").slideToggle();
        $(categoryHeader).siblings(".gamb-duplica-wp-help-category-list").slideToggle();
    };

    GambDuplicaWP.Help.Search = function(search) {
        let results   = $("#gamb-duplica-wp-help-search-results");
        let noResults = $("#gamb-duplica-wp-help-search-results-empty");
        let context   = $("#gamb-duplica-wp-context-articles");
        let articles  = $(".gamb-duplica-wp-help-article");
        let regex     = GambDuplicaWP.Help.GetRegex(search);

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

    GambDuplicaWP.Help.Load = function(url) {
        if (GambDuplicaWP.Help.isDataLoaded()) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function(xhr) {
                GambDuplicaWP.Util.ajaxProgressShow();
            },
            success: function (result) {
                GambDuplicaWP.Help.Data = result;
                //because ajax is async we need to open the modal here for first time
                GambDuplicaWP.Help.Display();
            },
            error: function (result) {
                DupPro.addAdminMessage(l10nDupDynamicHelp.failedLoad, 'error');
            },
            complete: function () {
                GambDuplicaWP.Util.ajaxProgressHide();
            },
        });
    };

    GambDuplicaWP.Help.Display = function() {
        if (!GambDuplicaWP.Help.isDataLoaded()) {
            throw 'gamb-duplica-wp.Help.Data is null';
        }

        let box = new GambDuplicaWPModalBox({
            htmlContent: GambDuplicaWP.Help.Data,
        });
        box.open();
    };

    GambDuplicaWP.Help.GetRegex = function(search = '') {
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
        GambDuplicaWP.Help.ToggleCategory(this);
    });

    $("body").on("keyup", "#gamb-duplica-wp-help-search input", function() {
        GambDuplicaWP.Help.Search($(this).val().toLowerCase());
    });
});
