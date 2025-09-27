(function ($) {
    var filter = {
        url: window.location.href,
        urlParams: null,
        filterObject: {},
        hasMorePosts: true,

        /**
         * Init
         */
        init: function () {
            var self = this;

            this.setUrlParams(this.getUrl());
            this.setFilterObjectByUrl(this.getUrlParams());
            this.updateActiveFilters(this.filterObject);
            this.ajaxPostCall(this.filterObject, true, function () {
                self.checkMorePosts();
            });
            this.ajaxFilterCall(this.filterObject, function () {
                self.updateActiveFilters(self.filterObject);
            });

            // Filter on click
            $(document).on('click', '.js-filter-link', function (event) {
                event.stopPropagation();
                self.setFilterObjectByFilterAttributes(self.filterObject, $(this));
                self.updateBrowserUrl(self.filterObject);
                self.ajaxPostCall(
                    self.filterObject,
                    true,
                    function () {
                        self.checkMorePosts();
                    }
                );
                self.ajaxFilterCall(self.filterObject, function () {
                    self.updateActiveFilters(self.filterObject);
                });
                self.scrollToTop($(this));
                event.preventDefault();
            });

            // Searching
            $(document).on('keyup', '.js-search', function (event) {
                event.stopPropagation();
                self.setFilterObjectBySearchKeyword(self.filterObject, $(this));
                self.updateBrowserUrl(self.filterObject);
                self.ajaxPostCall(
                    self.filterObject,
                    true,
                    function () {
                        self.checkMorePosts();
                    }
                );
                self.ajaxFilterCall(self.filterObject, function () {
                    self.updateActiveFilters(self.filterObject);
                });
                event.preventDefault();
            });

            // Range
            $(document).on('change', '.js-point-input', function (event) {
                event.stopPropagation();
                self.setFilterObjectByAmountRange(self.filterObject, $(this));
                self.updateBrowserUrl(self.filterObject);
                self.ajaxPostCall(
                    self.filterObject,
                    true,
                    function () {
                        self.checkMorePosts();
                    }
                );
                self.ajaxFilterCall(self.filterObject, function () {
                    self.updateActiveFilters(self.filterObject);
                });
                event.preventDefault();
            });

            $(document).on('input', '.js-point-input', function (event) {
                event.stopPropagation();
                self.updateRangeText();
                event.preventDefault();
            });

            $(document).on('click', '.js-filters-reset', function (event) {
                event.stopPropagation();
                self.resetFilterObject(self.filterObject);
                self.updateBrowserUrl(self.filterObject);
                self.ajaxPostCall(
                    self.filterObject,
                    true,
                    function () {
                        self.checkMorePosts();
                    }
                );
                self.ajaxFilterCall(self.filterObject, function () {
                    self.updateActiveFilters(self.filterObject);
                });
                event.preventDefault();
            });

            // Load more posts on click
            $(document).on('click', '.js-load-more', function (event) {
                if (self.hasMorePosts) {
                    self.loadMorePosts(self.filterObject, function () {
                        self.ajaxPostCall(
                            self.filterObject,
                            false,
                            function () {
                                self.checkMorePosts();
                            }
                        );
                    });
                }
            });
        },

        /**
         *
         * @returns {string}
         */
        getUrl: function () {
            return this.url;
        },

        /**
         * @param param
         */
        setUrl: function (param) {
            this.url = param;
        },

        /**
         * @returns {string}
         */
        getUrlParams: function () {
            return this.urlParams;
        },

        /**
         * @param url
         */
        setUrlParams: function (url) {
            this.urlParams = url
                ? url.split('?')[1]
                : window.location.search.slice(1);
        },

        /**
         *
         * @param filterObject
         * @param elem
         */
        setFilterObjectByFilterAttributes: function (filterObject, elem) {
            var obj = filterObject;
            var filterName = elem.closest('.filter').attr('data-filter');
            var filterValue = elem.closest('.js-filter-item').attr('data-value');

            if (obj.hasOwnProperty(filterName)) {
                var index = obj[filterName].indexOf(filterValue);

                if (index !== -1) {
                    // if filter values is in array
                    if (typeof obj[filterName] != 'string') {
                        obj[filterName].splice(index, 1);

                        if (obj[filterName].length === 0) {
                            delete obj[filterName];
                        }
                    } else {
                        delete obj[filterName];
                    }
                } else {
                    // if filter is stars
                    if (filterName == 'winetest_vince_stars') {
                        obj[filterName] = filterValue;
                    } else {
                        if (typeof obj[filterName] != 'string') {
                            obj[filterName].push(filterValue);
                        } else {
                            var currentValue = obj[filterName];
                            obj[filterName] = [];
                            obj[filterName].push(currentValue, filterValue);
                        }
                    }
                }
            } else {
                // if filter is stars
                if (filterName == 'winetest_vince_stars') {
                    obj[filterName] = filterValue;
                } else {
                    obj[filterName] = [];
                    obj[filterName].push(filterValue);
                }
            }

            this.filterObject = obj;
            this.filterObject['offset'] = 0;
            this.filterObject['current_page'] = 1;
            this.hasMorePosts = true;
        },

        /**
         *
         * @param filterObject
         * @param elem
         */
        setFilterObjectBySearchKeyword: function (filterObject, elem) {
            var obj = filterObject;
            var keyword = elem.val();

            if (keyword !== '' && keyword.length > 3) {
                obj['keyword'] = keyword;
            } else {
                delete obj['keyword'];
            }

            this.filterObject = obj;
            this.filterObject['offset'] = 0;
            this.filterObject['current_page'] = 1;
            this.hasMorePosts = true;
        },

        /**
         *
         * @param filterObject
         */
        setFilterObjectByAmountRange: function (filterObject) {
            var obj = filterObject;

            if (obj.hasOwnProperty('product_amount_min')) {
                delete obj['product_amount_min'];
            }

            if (obj.hasOwnProperty('product_amount_max')) {
                delete obj['product_amount_max'];
            }

            var minValue = $('#point-min').val();
            var maxValue = $('#point-max').val();

            if (minValue != '') {
                obj['winetest_vince_point_min'] = minValue;
            }

            if (maxValue != '') {
                obj['winetest_vince_point_max'] = maxValue;
            }

            this.filterObject = obj;
            this.offset = 0;
            this.currentPage = 1;
            this.hasMorePosts = true;
        },

        /**
         *
         * @param urlParams
         */
        setFilterObjectByUrl: function (urlParams) {
            var obj = {};

            if (urlParams) {
                // stuff after # is not part of query string, so get rid of it
                urlParams = urlParams.split('#')[0];

                // split our query string into its component parts
                var arr = urlParams.split('&');

                for (var i = 0; i < arr.length; i++) {
                    // separate the keys and the values
                    var a = arr[i].split('=');

                    // in case params look like: list[]=thing1&list[]=thing2
                    var paramNum = undefined;
                    var paramName = a[0].replace(/\[\d*\]/, function (v) {
                        paramNum = v.slice(1, -1);
                        return '';
                    });

                    // (optional) keep case consistent
                    paramName = paramName.toLowerCase();

                    // keep special characters
                    paramName = decodeURI(paramName);

                    // set parameter value (use 'true' if empty)
                    var paramValue = typeof a[1] === 'undefined' ? true : a[1];

                    // keep special characters
                    paramValue = decodeURI(paramValue);

                    if (paramValue.indexOf(',') != -1) {
                        // separate the values
                        paramValue = paramValue.split(',');
                    }

                    // if parameter name already exists
                    if (obj[paramName]) {
                        // convert value to array (if still string)
                        if (typeof obj[paramName] === 'string') {
                            obj[paramName] = [obj[paramName]];
                        }
                        // if no array index number specified...
                        if (typeof paramNum === 'undefined') {
                            // put the value on the end of the array
                            obj[paramName].push(paramValue);
                        }
                        // if array index number specified...
                        else {
                            // put the value at that index number
                            obj[paramName][paramNum] = paramValue;
                        }
                    }
                    // if param name doesn't exist yet, set it
                    else {
                        obj[paramName] = paramValue;
                    }
                }
            } else {
                obj['per_page'] = parseInt($('#product-list').attr('data-posts-per-page'));
                obj['offset'] = 0;
                obj['current_page'] = 1;
            }

            this.filterObject = obj;
        },

        /**
         *
         * @param filterObject
         * @param elem
         */
        resetFilterObject: function(filterObject) {
            var obj = filterObject;

            obj = {};

            obj['offset'] = 0;
            obj['current_page'] = 1;

            this.filterObject = obj;
        },

        /**
         * @param filterObject
         */
        updateBrowserUrl: function (filterObject) {
            var obj = filterObject;
            var paramsString;

            var params = [];
            var param;

            for (param in obj) {
                if (obj.hasOwnProperty(param)) {
                    params.push(encodeURI(param) + '=' + encodeURI(obj[param]));
                }
            }

            paramsString = params.join('&');

            window.history.pushState(obj, '', '?' + paramsString);

            this.setUrl(window.location.href);
        },

        /**
         * @param filterObject
         */
        updateActiveFilters: function (filterObject) {
            var obj = filterObject;

            $('.filter').each(function () {
                var filterName = $(this).attr('data-filter'),
                    filterItem = $(
                        '.filter[data-filter="' + filterName + '"]'
                    ).find('.js-filter-item');

                if (obj.hasOwnProperty(filterName)) {
                    filterItem.each(function () {
                        var filterValue = $(this).attr('data-value'),
                            index = obj[filterName].indexOf(filterValue);

                        if (index !== -1) {
                            $(this).addClass('is-active');
                        } else {
                            $(this).removeClass('is-active');
                        }
                    });
                }
            });

            // set keyword
            if (obj.hasOwnProperty('keyword')) {
                $('.js-search').val(obj['keyword']);
            }

            // set range
            if (
                obj.hasOwnProperty('product_amount_min') &&
                obj.hasOwnProperty('product_amount_max')
            ) {
                $('#product-amount-min').val(obj['product_amount_min']);
                $('#product-amount-max').val(obj['product_amount_max']);
                
                $('#product-amount-min-text').text(obj['product_amount_min'] + 'kg');
                $('#product-amount-max-text').text(obj['product_amount_max'] + ' kg');
            }
        },

        updateRangeText: function () {
            var minValue = $('#product-amount-min').val();
            var maxValue = $('#product-amount-max').val();

            $('#product-amount-min-text').text(minValue + ' ' + myAjax.point);
            $('#product-amount-max-text').text(maxValue + ' ' + myAjax.point);
        },

        scrollToTop: function (elem) {
            var filter = elem.closest('.filter'),
                filterName = filter.attr('data-filter');

            if (filterName == 'winetest_vince_stars') {
                $('html, body').scrollTop($('#body-top').offset().top);
            }

        },

        /**
         * Check more posts
         */
        checkMorePosts: function () {
            var maxPages = parseInt($('.js-load-more').attr('data-max-pages')) || 1;

            if (this.filterObject['current_page'] == maxPages) {
                this.hasMorePosts = false;
                $('.js-load-more').remove();
            }
        },

        /**
         * @param filterObject
         * @param callback
         */
        loadMorePosts: function (filterObject, callback) {
            var newPostsPerPage = filterObject['per_page'];
            var newOffset = filterObject['offset'];
            var newCurrentPage = filterObject['current_page'];

            // increase and set offset
            newOffset += newPostsPerPage;
            this.filterObject['offset'] = newOffset;

            // increase and set current page
            newCurrentPage++;
            this.filterObject['current_page'] = newCurrentPage;

            // call ajax
            callback();
        },

        initRange: function () {
            const rangeSlider = document.querySelectorAll('.range-slider');

            if (rangeSlider.length > 0) {
                const range = document.querySelectorAll('.range-slider input');
                const progress = document.querySelector('.range-slider .progress');
                let gap = 0;
                const inputValue = document.querySelectorAll('.numberVal input');
                let minRange = inputValue[0].value;
                let maxRange = inputValue[1].value;

                range.forEach((input) => {
                    input.addEventListener('input', (e) => {
                        minRange = parseFloat(range[0].value).toFixed(2);
                        maxRange = parseFloat(range[1].value).toFixed(2);

                        if (maxRange - minRange < gap) {
                            if (e.target.className === 'range-min') {
                                range[0].value = maxRange - gap;
                            } else {
                                range[1].value = minRange + gap;
                            }
                        } else {
                            progress.style.left =
                                ((minRange / range[0].max) * 100).toFixed(2) + '%';
                            progress.style.right =
                                (100 - (maxRange / range[1].max) * 100).toFixed(2) +
                                '%';
                            inputValue[0].value = minRange;
                            inputValue[1].value = maxRange;
                        }
                    });
                });
            }
        },

        /**
         *
         * @param filterObject
         * @param postType
         * @param replacement
         * @param callback
         * @constructor
         */
        ajaxPostCall: function (filterObject, replacement, callback) {
            if ($('#product-list').length) {
                $.ajax({
                    type: 'post',
                    url: localize.ajaxurl,
                    data: {
                        action: 'product_filter',
                        filter_object: filterObject,
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    success: function (response) {
                        if (replacement !== 'undefined') {
                            if (replacement) {
                                $('#product-list').html(response);
                            } else {
                                $('#product-list').append(response);
                            }
                        }
                    },
                    beforeSend: function () {
                        $('.js-load-more').remove();
                    },
                    complete: function () {
                        callback();
                    },
                });
            }
        },

        /**
         *
         * @param filterObject
         * @param postType
         * @param callback
         * @constructor
         */
        ajaxFilterCall: function (filterObject, callback) {
            if ($('#filter-list-vertical')) {
                $.ajax({
                    type: 'post',
                    url: localize.ajaxurl,
                    data: {
                        action: 'product_attributes_filter',
                        filter_object: filterObject,
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    success: function (response) {
                        $('#filter-list-vertical').html(response);
                    },
                    complete: function () {
                        callback();
                    },
                });
            }
        },
    };

    filter.init();
})(jQuery);
