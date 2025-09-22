import $ from 'jquery';
import 'slick-slider';
import imagesLoaded from "imagesloaded";
import Masonry from "masonry-layout";

let rawPostType = $('.section').attr('data-post-type');
let parsedPostType;

try {
    parsedPostType = JSON.parse(rawPostType); // handles ["post","knowledge_base"]
} catch (e) {
    parsedPostType = rawPostType; // fallback for "post"
}

var filter = {
    url: window.location.href,
    urlParams: null,
    filterObject: {},
    //postType: $('.section').attr('data-post-type'),
    postType: parsedPostType,
    hasMorePosts: true,

    /**
     * Init
     */
    init: function () {
        var self = this;

        this.setUrlParams(this.getUrl());
        this.setFilterObjectByUrl(this.getUrlParams());
        this.updateActiveFilters(this.filterObject);
        this.ajaxPostCall(this.filterObject, this.postType, function() {
            self.checkMorePosts();
            self.initMasonry();
            self.initSlider();
        });

        // Filter on click
        $(document).on('change', 'select.js-filter', function (event) {
            event.stopPropagation();
            self.setFilterObjectByFilterAttributes(self.filterObject, $(this));
            self.updateBrowserUrl(self.filterObject);
            self.ajaxPostCall(self.filterObject, self.postType, function() {
                self.checkMorePosts();
                self.initMasonry();
                self.initSlider();
            });
            event.preventDefault();
        });

        // Handle radio filters
        $(document).on('change', 'input[type="radio"].js-filter', function (event) {
            event.stopPropagation();
            self.setFilterObjectByFilterAttributes(self.filterObject, $(this));
            self.updateBrowserUrl(self.filterObject);
            self.ajaxPostCall(self.filterObject, self.postType, function() {
                self.checkMorePosts();
                self.initMasonry();
                self.initSlider();
            });
            event.preventDefault();
        });

        // Handle checkbox filters
        $(document).on('change', 'input[type="checkbox"].js-filter', function (event) {
            event.stopPropagation();
            self.setFilterObjectByFilterAttributes(self.filterObject, $(this));
            self.updateBrowserUrl(self.filterObject);
            self.ajaxPostCall(self.filterObject, self.postType, function() {
                self.checkMorePosts();
                self.initMasonry();
                self.initSlider();
            });
            event.preventDefault();
        });

        $(document).on('click', 'li.js-filter', function (event) {
            event.stopPropagation();

            const $this = $(this);
            const isActive = $this.hasClass('active');

            $('li.js-filter').removeClass('active');

            if (!isActive) {
                $this.addClass('active');
            }

            self.setFilterObjectByFilterAttributes(self.filterObject, $this, isActive);
            self.updateActiveFilters(self.filterObject);
            self.updateBrowserUrl(self.filterObject);
            self.ajaxPostCall(self.filterObject, self.postType, function() {
                self.checkMorePosts();
                self.initMasonry();
                self.initSlider();
            });
            event.preventDefault();
        });

        // Searching
        $(document).on('keyup', '.js-filter-search', function (event) {
            event.stopPropagation();
            self.setFilterObjectBySearchKeyword(self.filterObject, $(this));
            self.updateBrowserUrl(self.filterObject);
            self.ajaxPostCall(self.filterObject, self.postType, function() {
                self.checkMorePosts();
                self.initMasonry();
                self.initSlider();
            });
            event.preventDefault();
        });

        // Pagination
        if (self.hasMorePosts) {
            self.paginationPosts(self.filterObject, function () {
                self.updateBrowserUrl(self.filterObject);
                self.ajaxPostCall(self.filterObject, self.postType, function() {
                    self.checkMorePosts();
                    self.initMasonry();
                    self.initSlider();
                });
            });
        }
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
    /*
    setFilterObjectByFilterAttributes: function (filterObject, elem, shouldRemove = false) {
        var obj = filterObject;
        var filterName = elem.attr('data-filter');
        var selectedValues = shouldRemove ? null : (elem.attr('data-value') !== undefined ? elem.attr('data-value') : elem.val());

        if (!selectedValues || selectedValues.length === 0) {
            delete obj[filterName];
        } else {
            obj[filterName] = selectedValues;
        }

        this.filterObject = obj;
        this.filterObject['per_page'] = parseInt($('.section').attr('data-posts-per-page'));
        this.filterObject['offset'] = 0;
        this.filterObject['current_page'] = 1;
        this.hasMorePosts = true;
    },
    */

    setFilterObjectByFilterAttributes: function (filterObject, elem, shouldRemove = false) {
        var obj = filterObject;
        var filterName = elem.attr('data-filter');
        var inputType = elem.attr('type');
        var selectedValues;

        if (shouldRemove) {
            delete obj[filterName];
        } else {
            if (inputType === 'checkbox') {
                // collect all checked checkboxes for this filter group
                selectedValues = [];
                $('input[type="checkbox"][data-filter="' + filterName + '"]:checked').each(function () {
                    selectedValues.push($(this).val());
                });

                if (selectedValues.length > 0) {
                    obj[filterName] = selectedValues;
                } else {
                    delete obj[filterName];
                }

            } else if (inputType === 'radio') {
                // only one value can be set
                if (elem.is(':checked')) {
                    obj[filterName] = elem.val();
                } else {
                    delete obj[filterName];
                }

            } else {
                // fallback (select, li.js-filter, etc.)
                selectedValues = elem.attr('data-value') !== undefined ? elem.attr('data-value') : elem.val();

                if (!selectedValues || selectedValues.length === 0) {
                    delete obj[filterName];
                } else {
                    obj[filterName] = selectedValues;
                }
            }
        }

        this.filterObject = obj;
        this.filterObject['per_page'] = parseInt($('.section').attr('data-posts-per-page'));
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

        if (keyword !== '' && keyword.length > 2) {
            obj['keyword'] = keyword;
        } else {
            delete obj['keyword'];
        }

        this.filterObject = obj;
        this.filterObject['per_page'] = parseInt($('.section').attr('data-posts-per-page'));
        this.filterObject['offset'] = 0;
        this.filterObject['current_page'] = 1;
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
            obj['per_page'] = parseInt($('.section').attr('data-posts-per-page'));
            obj['offset'] = 0;
            obj['current_page'] = 1;
        }

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

        // filter list
        $('.filter--list').each(function () {
            var $filterList = $(this);
            var taxonomy = $filterList.find('li.js-filter').first().attr('data-filter');

            var selectedValue = obj.hasOwnProperty(taxonomy) ? obj[taxonomy] : null;

            $filterList.find('li.js-filter').each(function () {
                var $item = $(this);
                var value = $item.attr('data-value');

                if (selectedValue === null || selectedValue === '' || typeof selectedValue === 'undefined') {
                    if ($item.hasClass('all')) {
                        $item.addClass('active');
                    } else {
                        $item.removeClass('active');
                    }
                } else {
                    if (value === selectedValue) {
                        $item.addClass('active');
                    } else {
                        $item.removeClass('active');
                    }
                }
            });
        });

        // set keyword
        if (obj.hasOwnProperty('keyword')) {
            $('.js-filter-search').val(obj['keyword']);
        }
    },

    /**
     * Init masonry layout
     */
    initMasonry: function () {
        var grid = document.querySelector('.grid');

        if (grid) {
            var msnry = new Masonry(grid, {
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: true
            });
            
            imagesLoaded(grid, function() {
                msnry.layout();
            });
        }
    },

    /**
     * Init slick slider
     */
    initSlider: function () {
        const eventsSliderSelector = $(document).find('.slider--events .slider__list');

        if (eventsSliderSelector.length > 0) {
            eventsSliderSelector.each(function() {
                const eventsSliderControls = $(this).closest('.slider--events').find('.slider__controls');

                $(this).slick({
                    mobileFirst: true,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    variableWidth: true,
                    prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
                    nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
                    appendArrows: eventsSliderControls,
                });
            });
        }
    },

    /**
     * Check more posts
     */
    checkMorePosts: function() {
        var maxPages = parseInt($('.js-pagination').attr('data-max-pages')) || 1;

        if (this.filterObject['current_page'] == maxPages) {
            this.hasMorePosts = false;
        }
    },

    /**
     * @param filterObject
     * @param callback
     */
    paginationPosts: function (filterObject, callback) {
        const self = this;
        let newPostsPerPage = parseInt(filterObject['per_page']);
        let newOffset = parseInt(filterObject['offset']);
        let newCurrentPage = parseInt(filterObject['current_page']);

        const handlePaginationClick = (event, direction) => {
            event.preventDefault();
            event.stopPropagation();

            if (direction === 'next') {
                newOffset += newPostsPerPage;
                newCurrentPage++;
            } else if (direction === 'prev') {
                newOffset -= newPostsPerPage;
                newCurrentPage--;
            } else {
                const pageNumber = parseInt($(event.currentTarget).data('number'));
                newOffset = (pageNumber * newPostsPerPage) - newPostsPerPage;
                newCurrentPage = pageNumber;
            }

            self.filterObject['offset'] = newOffset;
            self.filterObject['current_page'] = newCurrentPage;

            callback();
        };

        $(document).on('click', '.js-pagination-link.next', (event) => handlePaginationClick(event, 'next'));
        $(document).on('click', '.js-pagination-link.prev', (event) => handlePaginationClick(event, 'prev'));
        $(document).on('click', '.js-pagination-link.number', (event) => handlePaginationClick(event));
        $(document).on('click', '.js-pagination-link.first', (event) => handlePaginationClick(event));
        $(document).on('click', '.js-pagination-link.last', (event) => handlePaginationClick(event));
    },

    /**
     *
     * @param filterObject
     * @param postType
     * @param callback
     * @constructor
     */
    ajaxPostCall: function (filterObject, postType, callback) {
        if ($('#post-list-upcoming').length) {
            $.ajax({
                type: 'post',
                url: localize.ajaxurl,
                data: {
                    action: 'post_filter',
                    post_type: postType,
                    event_type: 'upcoming',
                    filter_object: filterObject,
                },
                error: function (response) {
                    console.log(response);
                },
                success: function (response) {
                    $('#post-list-upcoming').html(response);
                },
                complete: function () {
                    callback();
                },
            });
        }

        if ($('#post-list-past').length) {
            $.ajax({
                type: 'post',
                url: localize.ajaxurl,
                data: {
                    action: 'post_filter',
                    post_type: postType,
                    event_type: 'past',
                    filter_object: filterObject,
                },
                error: function (response) {
                    console.log(response);
                },
                success: function (response) {
                    $('#post-list-past').html(response);
                },
                complete: function () {
                    callback();
                },
            });
        }
        
        if ($('#post-list').length) {
            $.ajax({
                type: 'post',
                url: localize.ajaxurl,
                data: {
                    action: 'post_filter',
                    post_type: postType,
                    filter_object: filterObject,
                },
                error: function (response) {
                    console.log(response);
                },
                success: function (response) {
                    $('#post-list').html(response);
                },
                complete: function () {
                    callback();
                },
            });
        }

        if ($('#bookmark-list').length) {
            $.ajax({
                type: 'post',
                url: localize.ajaxurl,
                data: {
                    action: 'post_filter',
                    post_type: postType,
                    post_ids: JSON.parse($('.section').attr('data-post-ids')),
                    filter_object: filterObject,
                },
                error: function (response) {
                    console.log(response);
                },
                success: function (response) {
                    $('#bookmark-list').html(response);
                },
                complete: function () {
                    callback();
                },
            });
        }
    },
};

filter.init();
